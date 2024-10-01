<?php

    public function createDocument(Request $request)
    {
        $solicitudeId = $request->input('solicitude_id');
        $title = "Documento para Solicitud $solicitudeId";
        $defaultOwnerEmail = 'paisi.udh@gmail.com'; // el editor predeterminado

        // Crear el documento en Google Docs
        $document = new Google_Service_Docs_Document([
            'title' => $title
        ]);

        try {
            // Crear el documento en Google Docs
            $createdDocument = $this->docsService->documents->create($document);
            $documentId = $createdDocument->getDocumentId();

            if (!$documentId) {
                return response()->json(['error' => 'No se pudo crear el documento'], 500);
            }

            // Asignar permisos de escritura al propietario predeterminado
            $editorPermission = new Google_Service_Drive_Permission();
            $editorPermission->setType('user');
            $editorPermission->setRole('writer'); // Rol de editor
            $editorPermission->setEmailAddress($defaultOwnerEmail);

            $this->driveService->permissions->create($documentId, $editorPermission);

            // Mover documento a una carpeta del drive
            $folderId = '1Diiq8CbTzB5EdXvZCJnEEq4MEMOUiA8I'; // Reemplaza con el folderId
            $emptyFileMetadata = new Google_Service_Drive_DriveFile();
            $this->driveService->files->update($documentId, $emptyFileMetadata, [
                'addParents' => $folderId,
                'removeParents' => 'root',
                'fields' => 'id, parents'
            ]);

            // Obtener el enlace de visualización desde Google Drive
            $file = $this->driveService->files->get($documentId, ['fields' => 'webViewLink']);
            $link = $file->getWebViewLink();

            if (!$link) {
                return response()->json(['error' => 'No se pudo obtener el enlace del documento'], 500);
            }

            // Actualizar la solicitud con el enlace del documento
            $solicitude = Solicitude::find($solicitudeId);
            if ($solicitude) {
                $solicitude->document_link = $link;
                $solicitude->save();
            } else {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            return response()->json(['link' => $link]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
<?php

namespace App\Http\Controllers;
use Aws\S3\S3Client;  
use Illuminate\Http\Request;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function UploadFile(Request $request){
        
        $request->validate([
            'file' => 'required|mimes:pdf,docx,doc,pptx,ppt,xls,xlsx|max:2048'
        ]);

        $file = $request->file('file');
        $path = Storage::putFile('uploads', $file);
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

       
        $keyname = 'uploads/' . $file->getClientOriginalName();
        $bucket = 'my-bucket813211';
        Storage::disk('local')->delete($path);

       try {
        $s3->headBucket([
            'Bucket' => $bucket,
        ]);
        } catch (S3Exception $e) {
        
            if ($e->getStatusCode() === 404) {
                $s3->createBucket([
                    'Bucket' => $bucket,
                ]);
            } else {
                return response()->json([
                    'error' => $e->getMessage()
                ]);
            }
        }

        try {
            $result = $s3->putObject([
                'Bucket' => 'my-bucket813211',
                'Key'    => $keyname,
                'Body'   => storage_path('app/' . $path),
                'ACL'    => 'public-read'
            ]);

            // Print the URL to the object.
            return response()->json([
                'message' => 'File uploaded successfully',
                'file link' => $result['ObjectURL']
            ]);
        } catch (S3Exception $e) {
            return response()->json([
                'Upload Failed' => $e->getMessage()
            ]);
        }
    }
}


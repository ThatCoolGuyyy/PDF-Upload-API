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
            'file' => 'required|mimes:pdf,docx,doc,pptx,ppt,xls,xlsx,jpg|max:2048'
        ]);

        $file = $request->file('file');
        

        //create s3 client
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        $keyname = 'uploads/' . $file->getClientOriginalName();
        
        //create bucket
        if (!$s3->doesBucketExist(env('AWS_BUCKET'))) {
            // Create bucket if it doesn't exist
            try{
                $s3->createBucket([
                    'Bucket' => env('AWS_BUCKET'),
                ]);
            } catch (S3Exception $e) {
                return response()->json([
                    'Bucket Creation Failed' => $e->getMessage()
                ]);
            }
        }
        //upload file
        try {
            $result = $s3->putObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => $keyname,
                'Body'   => fopen($file, 'r'),
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


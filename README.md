# AWS S3 File Upload API with PHP
This Laravel controller handles file uploads to an AWS S3 bucket. It uses the AWS SDK for PHP to interact with the S3 service.

## Setup
- Clone the repository.
- Run `composer install`.
- Configure your AWS credentials in the `.env` file as follows:
```
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_access_key
AWS_DEFAULT_REGION=your_aws_region
```

## Usage
Send a POST request to `/upload` with a file in the file parameter. The file must be a PDF, DOCX, DOC, PPTX, PPT, XLS, or XLSX file and must not exceed 2 MB in size.(You can change the file size limit in the UploadController.php file.)

The controller will upload the file to the AWS S3 bucket specified in the .env file and return a JSON response with the URL of the uploaded file.


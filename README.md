# Bulk upload receipts to Wave for processing

Wave allow you to upload receipts via email to receipts@waveapps.com to queue for processing.

https://support.waveapps.com/hc/en-us/articles/208622136-How-to-email-your-receipts-to-your-Wave-account

This script allows you to bulk email PDF and images to Wave. Saves tons of time by manually uploading using browser or mobile app.

## Insallation

### Composer (Preferred)

`composer create-project jinjie/upload-waveapps-receipts`

### Clone

```bash
git clone https://github.com/jinjie/upload-waveapps-receipts.git
cd upload-waveapps-receipts
composer install
```

## Usage

`./upload <directory or path to image>`

## Options


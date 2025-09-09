
				cd C:/Program Files/wkhtmltopdf/bin
				wkhtmltopdf --encoding UTF-8 -B 7mm -T 7mm -L 9mm -R 9mm --header-font-size 8 --header-font-name calibri --header-left "Page [page] of [topage]" --header-right "Control No.: 0000042 Account No. :55929" "lse.libcapsystems.net/admin/soa/soa_attachment/bf8b0edc519de61de60bc54f71b05c9dbf8b0edc519de" C:/wamp64/www/LSE_NEW_BACKEND/storage/app/public/soa_attachment/CTRL_0000042_bf8b0edc519de61de60bc54f71b05c9dbf8b0edc519de.pdf
				cd C:\wamp64\www\LSE_NEW_BACKEND
				C:\wamp64\bin\php\php7.4.9\php.exe artisan soa:update_generated --token=bf8b0edc519de61de60bc54f71b05c9dbf8b0edc519de
				exit;
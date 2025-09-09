
				cd C:/Program Files/wkhtmltopdf/bin
				wkhtmltopdf "lse.libcapsystems.net/admin/soa/soa_attachment/1ed677589d8589beacdc37bc514f16321ed677589d858" C:/wamp64/www/LSE_NEW_BACKEND/storage/app/public/soa_attachment/CTRL_0000049_1ed677589d8589beacdc37bc514f16321ed677589d858.pdf
				cd C:\wamp64\www\LSE_NEW_BACKEND
				C:\wamp64\bin\php\php7.4.9\php.exe artisan soa:update_generated --token=1ed677589d8589beacdc37bc514f16321ed677589d858
				exit;
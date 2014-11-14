ALTER TABLE `RelNodeTypeMimeType` MODIFY COLUMN `extension` varchar(255) NULL;

UPDATE `RelNodeTypeMimeType` SET mimeString='text/plain|text/x-php|image/svg+xml',extension=';txt;js;json;php;coffee;svg;' WHERE idRelNodeTypeMimeType=39;
UPDATE `RelNodeTypeMimeType` SET mimeString='image/jpeg|image/png|image/gif|image/x-icon|image/x-ms-bmp',extension=';jpeg;jpg;gif;png;ico;bmp;' WHERE idRelNodeTypeMimeType=40;
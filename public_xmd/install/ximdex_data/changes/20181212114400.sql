UPDATE `PipeProcess` SET `Name` = 'HTMLToXIF' WHERE `PipeProcess`.`id` = 8;
UPDATE `PipeProcess` SET `Name` = 'XIFToPublished' WHERE `PipeProcess`.`id` = 9;

UPDATE `PipeTransitions` SET `Name` = 'PublishXIF' WHERE `PipeTransitions`.`id` = 13;
UPDATE `PipeTransitions` SET `Name` = 'PrepareXIF', `Callback` = 'PrepareXIF' WHERE `PipeTransitions`.`id` = 12;

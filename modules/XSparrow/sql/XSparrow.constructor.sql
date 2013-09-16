insert into Actions (IdAction,Idnodetype,name, command, icon, description, sort, module) VALUES(9000,5012, "Create a new wizard project", "createproject", "create_proyect.png", "Create a new wizard project", 10, "XSparrow");
INSERT INTO RelRolesActions (IdRel,IdRol,IdAction,IdState,IdContext,IdPipeline) VALUES (NULL,201,9000,0,1,3);

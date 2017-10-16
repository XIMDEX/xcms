DELETE FROM NodeTypes where IdNodeType = 5501;
DELETE FROM NodeTypes where IdNodeType = 5502;

DELETE FROM NodeAllowedContents where IdNodeType = 5501;
DELETE FROM NodeAllowedContents where IdNodeType = 5502;
DELETE FROM NodeAllowedContents where NodeType = 5502;
DELETE FROM NodeAllowedContents where IdNodeType = 5022 and NodeType=5502;

DELETE FROM RelNodeTypeMimeType where idNodeType = 5501;
DELETE FROM RelNodeTypeMimeType where idNodeType = 5502;

DELETE FROM Actions where IdAction in(8501,8502,8503,8504,8505);

DELETE FROM NodeDefaultContents where IdNodeType = 8501;
DELETE FROM NodeDefaultContents where IdNodeType = 8502;

DELETE FROM NodeDefaultContents where NodeType = 5502;

DELETE FROM RelRolesActions WHERE IdAction = 8501;
DELETE FROM RelRolesActions WHERE IdAction = 8502;
DELETE FROM RelRolesActions WHERE IdAction = 8503;
DELETE FROM RelRolesActions WHERE IdAction = 8504;
DELETE FROM RelRolesActions WHERE IdAction = 8505;

DROP TABLE RelNodeMetaData;

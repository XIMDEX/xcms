<?php
$I = new AcceptanceTester($scenario);

if(file_exists('conf/_STATUSFILE')){
    $I->deleteFile('conf/_STATUSFILE');
}

if( file_exists('data/previos/css/default.css') ){
    $I->deleteFile('data/previos/css/default.css');
    $I->deleteDir('data/previos/css');
}

if( file_exists('data/previos/picasso-iden-idhtml.html') ){
    $I->deleteFile('data/previos/picasso-iden-idhtml.html');
}

$I->wantTo('ensure that installation works');

$I->amOnPage('/setup/index.php');

$I->see("Welcome to Ximdex CMS");

$I->click("Start Installation");

$I->waitForText("System Requirements", 5);

//$I->click("Check System");
$I->click("Start Installation");

$I->waitForText("Database host:", 5);

$I->fillField("dbhost", "db");
$I->fillField("dbuser", "ximdex");
$I->fillField("dbpass", "ximdex");
$I->fillField("dbname", "ximdex");

//$I->click("Check Connection to DB");
$I->click("Check");

$I->wait(3);

$I->click("Continue: Create tables");

$I->waitForText("Tables and default data have been created", 20);

$I->click("Continue: Install Modules");

$I->waitForText("Install Modules", 10);

$I->click("Next: Settings");

$I->see("Set the password for the user Ximdex (Administrator)");

$I->fillField("password", "ximdex");
$I->fillField("repeatpassword", "ximdex");

$I->click("Check");

$I->click("Enjoy Ximdex !");

$I->see("User");
$I->see("Password");

$I->fillField("user", "ximdex");
$I->fillField("password", "ximdex");

$I->click("Sign in");

$I->waitForText("Welcome to Ximdex CMS, ximdex!", 10);

$I->wantTo('ensure that publish works');

$I->waitForText("Hello ximdex, first time here?", 5, "#tourcontrols");
$I->click("#canceltour");

function reload($I){
    $I->click("#angular-tree > div.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.tabs-container.hbox-panel.ng-isolate-scope > div.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.tabs-container > div > div.browser-view.ui-tabs-panel.ui-widget-content.ui-corner-bottom.tab-pane.ng-scope.active > div.ng-scope > xim-tree > div > div.xim-treeview-btnreload.ui-corner-all.ui-state-default.ng-binding");
}

$I->click("//span[contains(text(),'Picasso')]", "#angular-tree");
reload($I);

$I->waitForText("Picasso_Server", 5, "#angular-tree");

$I->click("//span[contains(text(),'Picasso_Server')]", "#angular-tree");
reload($I);
$I->waitForText("documents", 5, "#angular-tree");

$I->click("//span[contains(text(),'documents')]", "#angular-tree");
reload($I);
$I->waitForText("picasso", 5, "#angular-tree");

$I->click("//span[contains(text(),'picasso')]", "#angular-tree");
reload($I);
$I->waitForText("picasso-iden", 5, "#angular-tree");

// Open picasso-iden menu
$I->click("//*[@id=\"angular-tree\"]/div[1]/div[2]/div/div[1]/div[2]/xim-tree/div/div[2]/ul/li/tree-node/span/ul/li/span/ul/li[4]/span/ul/li[3]/span/ul/li[1]/span/ul/li/span/div/span[2]");

$I->waitForText("Publish", 3, "body > div.xim-actions-menu.destroy-on-click.noselect.xim-actions-menu-list");

$I->click("body > div.xim-actions-menu.destroy-on-click.noselect.xim-actions-menu-list > div.button-container-list.icon.workflow_forward");

$I->wait(3);

$I->click("Publish", "#angular-content");

$I->waitForText("State has been successfully changed", 3, "#angular-content");

function fileExistAndIsNotEmpty($path){
    return file_exists($path) && filesize($path);
}

$count = 0;
while(!fileExistAndIsNotEmpty('data/previos/css/default.css') && $count < 45){
    sleep(2);
    $count++;
}

while(!fileExistAndIsNotEmpty('data/previos/picasso-iden-idhtml.html') && $count < 45){
    sleep(2);
    $count++;
}

$I->seeFileFound('default.css','data/previos/css');
$I->seeFileFound('picasso-iden-idhtml.html','data/previos');

$I->amOnPage("/data/previos/picasso-iden-idhtml.html");

$I->see("Picasso", ".header");
$I->see("Cubism", ".header");
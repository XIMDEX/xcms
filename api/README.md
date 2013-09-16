		Instructions to configure the Ximdex's API.
                ------------------------------------------   

In order to configure the Ximdex's API, you need first to do this two steps:

a) Obtain the API-KEY executing this command into a system console (you will need the openssl package):

	$> openssl enc -aes-128-cbc -k "MY_SECRET_PHRASE" -P -md sha1

being MY_SECRET_PHRASE a string or sentence invented by you. Write down the 'iv' and 'key' values returned.


b) Executing SQL queries (you need access to the Ximdex CMS database):

	UPDATE CONFIG SET ConfigValue=”key_value” where ConfigKey=”ApiKey”;
	UPDATE CONFIG SET ConfigValue=”iv_value” where ConfigKey=”ApiIV”;

being 'key_value' and 'iv_value' the values annotated in the first step.


And that's all. Ximdex API is now prepared to dispatch your requests!

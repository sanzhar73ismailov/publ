<?php
class User1{
	public $name;
	public $age;
	
}


function checkGetPublicationsRespCoauthor (){
	
	global $dao;
	$entity = new Publication();
	$pubs = $dao->getPublicationsRespCoauthor(5);
	//var_dump($pubs);
	
}

function test_object(){
	$user = new User1();
	$f_name = "name";
	//$user->{"name"} = "123123";
	$user->{$f_name} = "777555123123";
	echo $user->name;
	
	
}

function test_json(){
	
	$obj = new User1();
	$obj->name = "Вася";
	$obj->age = 11;
	
	$obj2 = new User1();
	$obj2->name = "John";
	$obj2->age = 16;
	
	$coded1 = json_encode($obj); 
	echo $coded1;
	
	$coded2 = json_encode($obj2); 
	echo $coded2;
	
	$decoded = json_decode($coded1); 
	var_dump($decoded);
}

function test_mail(){
	
	$to = "sanzhar73@gmail.com";
	$from= ADMIN_EMAIL; 
	$subject= "тест"; 
	$message= "привет";
	echo "<h1>test mail</h1><br>"; 
	
	$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	$headers .= "From: $from\r\n";

	
	
	$res = mail_utf8($to, $from, $subject, $message);
	//$res = mail($to, $subject, $message, $headers);
	echo "<h1>result:". $res . "</h1><br>";
	

	/*
$message = "Line 1\nLine 2\nLine 3";

// На случай если какая-то строка письма длиннее 70 символов мы используем wordwrap()
$message = wordwrap($message, 70);

// Отправляем

$res= mail('sanzhar73@gmail.com', 'My Subject', $message);
echo "<h1>result:". $res . "</h1><br>";
*/
}
function get_publ_array(){
	$arr = array("id",
  "name",
  "abstract_original",
  "abstract_rus",
  "abstract_kaz",
  "abstract_eng",
  "language",
  "keywords",
  "number_ilustrations",
  "number_tables",
  "number_references",
  "number_references_kaz",
  "code_udk",
  "type_id",
  "journal_id",
  "journal_name",
  "journal_country",
  "journal_issn",
  "journal_periodicity",
  "journal_izdatelstvo_mesto_izdaniya",
  "year",
  "month",
  "day",
  "number",
  "volume",
  "issue",
  "p_first",
  "p_last",
  "pmid",
  "conference_name",
  "conference_city",
  "conference_country",
  "conference_type_id",
  "conference_level_id",
  "conference_type_pub_id",
  "conference_date_start",
  "conference_date_finish",
  "patent_type_id",
  "patent_type_number",
  "patent_date",
  "book_city",
  "book_pages",
  "izdatelstvo",
  "user");
	return $arr;
}

function create_script(){
	$arr = get_publ_array();
	foreach ($arr as $value){
		//echo sprintf("\$investigation->%s= \$this->getNullForObjectFieldIfStringEmpty(\$request['%s']);<br>",$value,$value);
		//echo sprintf("\$investigation->%s=\$row[0]['%s'];<br>", $value,$value);
		//echo sprintf("<td>{if \$item->%s == null} - {else} + {/if}</td>\n", $value);

		//echo sprintf("<th>%s</th>\n", ++$i);

		//echo sprintf("\$investigation->%s= \$this->getNullForObjectFieldIfStringEmpty(\$request['%s']);<br>",$value,$value);
		//echo sprintf("\$investigation->%s=\$row[0]['%s'];<br>", $value,$value);
		echo sprintf("\$stmt->bindValue(':%s', \$this->object->%s, PDO::PARAM_STR);<br>", $value,$value);


	}

}
function checkUser(){
	//$obj = new User();
	//$obj->first_name="Петр";
	//$_SESSION['user1'] = $obj;
	//var_dump($obj);
	var_dump($_SESSION['user']['first_name']);

}

function test_insert_publication($id=null){

	global $dao;
	$entity = new Publication();
	$entity->id = $id;
	$object = TestObjectCreator::createTstObject($entity);
	var_dump($object);
	echo "<p><p>----------------";
	$insertId =$dao->insert($object);
	echo("insertId=$insertId<br>");
	return $insertId;
}


function test_insert_author(){
	global $dao;
	$object = TestObjectCreator::createTstObject(new Author());
	$insertId =$dao->insert($object);
	echo("insertId=$insertId<br>");
	return $insertId;
}

function test_insert_reference(){
	global $dao;
	$object = TestObjectCreator::createTstObject(new Reference());
	$insertId =$dao->insert($object);
	echo("insertId=$insertId<br>");
	return $insertId;
}

function test_select_author(){
	global $dao;
	$insertId =test_insert_author();
	//echo(  "insertId=$insertId<br>");

	$objectToGet = new Author();
	$objectToGet->id = $insertId;
	$objectToGet = $dao->get($objectToGet);
	var_dump($objectToGet);
}

function test_select_reference(){
	global $dao;
	$insertId =test_insert_reference();
	//echo(  "insertId=$insertId<br>");

	$objectToGet = new Reference();
	$objectToGet->id = $insertId;
	$objectToGet = $dao->get($objectToGet);
	var_dump($objectToGet);
}

function test_select_author_by_condition(){
	global $dao;


	// $pub_id = test_insert_publication();
	$pub_id = 82398;
	$publQuery =FabricaQuery::createQuery($dao->getPdo(), new Author());
	$arr = $publQuery->selectQueryManyByCondition(array(
	new QueryCondition("publication_id", $pub_id, "<"),
	new QueryCondition("first_name", '%гоша', "like")
	), "last_name desc");

	var_dump($arr);
}

function test_select_reference_by_condition(){
	global $dao;


	// $pub_id = test_insert_publication();
	$pub_id = 36377;
	$publQuery =FabricaQuery::createQuery($dao->getPdo(), new Reference());
	$arr = $publQuery->selectQueryManyByCondition(array(
	new QueryCondition("publication_id", $pub_id, "=")
	), "name desc");

	var_dump($arr);
}

function test_select_publicatio_by_id($id=null){
	global $dao;


	// $pub_id = test_insert_publication();
	$pub_id = $id != null ? $id : 36377;
	$objectToGet = new Publication();
	$objectToGet->id = $pub_id;
	$objectToGet = $dao->get($objectToGet);

	return $objectToGet;
}

function test_update_publication_by_id(){
	global $dao;
	// $pub_id = test_insert_publication();
	$pub_id = 49897;
	$objectToGet =  TestObjectCreator::createTstObject(new Publication());
	$objectToGet->id = $pub_id;
	$dao->update($objectToGet);
	$objectToGet = $dao->get($objectToGet);



	var_dump($objectToGet);
}

function test_big_update_publication_by_id(){
	global $dao;
	//$insertId = test_insert_publication(1);
	$insertId = 1;

	$publFromDb = test_select_publicatio_by_id($insertId);

	$publFromDb->name = "123Нанотехнологии123 909090";
	$publFromDb->authors_array[2]->last_name = "Петрюшин123";

	$authNew = new Author();
	$authNew->last_name="Аношин555";
	$publFromDb->authors_array[] = $authNew;

	$authNew = new Author();
	$authNew->last_name="Митрофанов555";
	$publFromDb->authors_array[] = $authNew;

	unset($publFromDb->authors_array[3]);

	$dao->update($publFromDb);

	$publFromDb = test_select_publicatio_by_id($insertId);
	echo "<p>\n";
	var_dump($publFromDb);

}

function test_delete_item(){
	global $dao;
	$ins_id = test_insert_publication();
	//$ins_id=781861;

	//588746 452271 612458 573945


	//test_insert_reference();
	//test_insert_publication();

	$entity = new Publication();
	$entity->id=$ins_id;
	echo($dao->delete($entity));

}

function test_arrays(){

	$array1 = array(3,4,6,1,89,22);
	$array2 = array(5,3,4,6,1,89,77, 22);

	$arr_dif = array_diff($array2, $array1);


	//var_dump($arrayUnsorted);
	//sort($arrayUnsorted);
	echo "<br>";
	var_dump($array1);
	echo "<br>";
	var_dump($array2);
	echo "<br>";
	var_dump($arr_dif);



}

function test_select_publicationUser_by_user_id($user_id){
	global $dao;
	//$insertId = test_insert_publication(1);
	$dao->getMyPublications($user_id);
}


?>
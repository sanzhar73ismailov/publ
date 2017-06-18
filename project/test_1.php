<?php
session_start();
include_once 'includes/global.php';
include_once 'includes/test_units.php';

function run(){
	//test_insert_publication();
	//test_insert_author();
	//test_insert_reference();
	//test_select_author();
	//test_select_reference();
	//	test_select_author_by_condition();
	//test_select_reference_by_condition();
	//test_select_publicatio_by_id();
	//test_update_publicatio_by_id();
	
	
	//test_big_update_publication_by_id();
	
	//test_arrays();
	//test_delete_item();
	//checkUser();
	
	//test_select_publicationUser_by_user_id(6);
	//create_script();
	//test_mail();
	//test_json();
	//test_object();
	checkGetPublicationsRespCoauthor();
	
	

}




run();
?>

<script>
/*
console.log(Math.floor(Math.random()));
var array = [[1, "Статья из периодического издания"], 
 			[2, "Книга"], 
 			[3, "Публикация из материалов конференции(семинара, симпозиума), сборников трудов"], 
             [4, "Электронный ресурс"]];

for(var i = 0; i <  array.length; i++){
	console.log(array[i][0] + " - " + array[i][1]);
}
*/
</script>

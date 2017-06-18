<?php
include_once "includes/global.php";
function getRandom(){
	return rand(1, 1000000);
}
class TestObjectCreator{

	public static function createTstObject($obj, $isForArray = 0){

		$rand = getRandom();
		$object = null;


		//echo($isForArray == 1? "" : get_class($obj));

		switch(get_class($obj)){

			case "Publication":
				$object = new Publication();
				$object->id = $obj->id == null ? $rand : $obj->id ;
				$object->name= "Наименование_" . $rand;
				$object->abstract_original="Резюме на ориг языке_" . $rand;
				$object->abstract_rus="Резюме на русском_" . $rand;
				$object->abstract_kaz="Резюме на каз_" . $rand;
				$object->abstract_eng="Резюмен на англ_" . $rand;
				$object->language="ЯзыкПубл_" . $rand;
				$object->keywords="Ключ слова_" . $rand;
				$object->number_ilustrations=  rand(1,10);
				$object->number_tables=rand(1,10);
				$object->number_references=rand(1,10);
				$object->number_references_kaz=rand(1,10);
				$object->code_udk="код удк _" . $rand;
				$object->type_id=rand(1,5);
				$object->journal_id=rand(1,100);
				$object->journal_name="Название журнала_" . $rand;
				$object->journal_country="Страна_" . $rand;
				$object->journal_issn="issn_" . $rand;
				$object->journal_periodicity=rand(1,12);
				$object->journal_izdatelstvo_mesto_izdaniya="journal_izdatelstvo_mesto_izdaniya_" . $rand;
				$object->year=rand(2011,2015);
				$object->month=rand(1,12);
				$object->day=rand(1,28);
				$object->number=rand(1,12);
				$object->volume=rand(1,100);
				$object->issue=rand(1, 7);
				$object->p_first=rand(1, 1000);
				$object->p_last=rand(1, 1000);
				$object->pmid=rand(1, 1000000);
				$object->user_id=rand(1, 1000000);
				$object->conference_id=rand(1, 1000000);
				$object->izdatelstvo="izdatelstvo=_" . $rand;
				$object->authors_array = self::createArrayObjects(new Author());
				$object->references_array = self::createArrayObjects(new Reference());


				break;
			case "Author":
				$object = new Author();
				//$object->id= $rand;
				$object->publication_id=$rand-10;
				$object->last_name= "Фамилия" . $rand;
				$object->first_name= "Имя_" . $rand;
				$object->patronymic_name= "Отчество_" . $rand;
				$object->organization_name= "Наименование организации_" . $rand;
				$object->organization_id=$rand-20;
				$object->user =  "Пользователь_" . $rand;
				$object->insert_date = null;
				break;
			case "Reference":
				$object = new Reference();
				//$object->id= $rand;
				$object->publication_id=$rand-10;
				$object->type_id= $rand-20;
				$object->name= "Наименование ссылки_" . $rand;
				$object->user =  "Пользователь_" . $rand;
				$object->insert_date = null;
				break;
			case "User":
				$object = new User();
				$object->id = 1;
				$object->username_email = "java_jana_mail.ru";
				$object->password = "";
				$object->last_name = "Тестов";
				$object->first_name = "Тест";
				$object->patronymic_name = "Тестович";
				$object->departament = "Отеделение тестовове";
				$object->status = "Зав. отделением";
				$object->sex_id = "1";
				$object->date_birth = null;
				$object->project = "publ";
				$object->comments = null;
				break;
			default:
				exit("Unsupported object");
				break;
		}





		return $object;

	}

	public static function createArrayObjects($obj){
			
		$numberArray = 3; //getRandom(1, 10);
		$object = null;
		$array = null;

		for ($i = 0; $i < $numberArray; $i++) {
			$object = self::createTstObject($obj, 1);
			$array[$i] = $object;
		}
		return $array;



	}

}
?>


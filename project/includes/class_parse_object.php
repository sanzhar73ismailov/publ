<?php
include_once 'includes/class_publication.php';

abstract class ParseForm{
	protected  $parsedObject;
	protected $request;


	public function __construct($request){
		foreach ($request as $key => $value){
			$request[$key] = $this->getNullForObjectFieldIfStringEmpty($value);
		}
		if(isset($_SESSION['user'])){
			$request['user'] = $_SESSION['user']['username_email'];
			//var_dump($_SESSION['user']);
		}
		$this->request=$request;
	}

	public abstract function parse();




	protected function getNullForObjectFieldIfStringEmpty($val){

		if(gettype($val) == "array"){
			foreach ($val as $key => $value){
				$val[$key] = $this->getNullForObjectFieldIfStringEmpty($value);
			}
			return $val;
		}

		if(!isset($val)){
			return null;
		}
		if($val == null){
			return null;
		}
		//$val = trim(mysql_real_escape_string($val));
		$val = trim($val);
		$val = strval($val);
		//echo strlen ($str) . "<br>";
		if(strlen ($val) == 0){
			return null;
		}
		return 	$val;
	}

	public  function getParsedObject(){
		return  $this->parsedObject;
	}
}

class ParsePublication extends ParseForm{



	public function __construct($request){
		parent::__construct($request);
		//$this->request=$request;
	}



	public function parse(){
		$this->parsedObject = new Publication();
		$object = $this->parsedObject;
		$request=$this->request;

		$fields = $object->getFields();

		foreach ($fields as $f_name => $value) {
			if(isset($request[$f_name])){
				if($f_name=='patent_date'){
					//$object->patent_date=getDateFromFormatDate($request['patent_date']);

					$object->$f_name = getDateFromFormatDate($request[$f_name]);
				}else{

					$object->$f_name = $request[$f_name];
				}
			}
		}

		if(isset($request['coauthor'])){
			$object->coauthor = $request['coauthor'];
		}else{
			exit("in class_parse_object: there no request['coauthor']");
		}

		/*
		 $object->id=$request['id'];
		 $object->name=$request['name'];

		 $object->electron=$request['electron'];
		 $object->url=$request['url'];
		 $object->doi=$request['doi'];

		 	
		 $object->abstract_original=$request['abstract_original'];
		 $object->abstract_rus=$request['abstract_rus'];
		 $object->abstract_kaz=$request['abstract_kaz'];
		 $object->abstract_eng=$request['abstract_eng'];
		 $object->language= isset($request['language']) ? $request['language'] : null;
		 $object->keywords=$request['keywords'];
		 $object->number_ilustrations=$request['number_ilustrations'];
		 $object->number_tables=$request['number_tables'];
		 $object->number_references=$request['number_references'];
		 $object->number_references_kaz=$request['number_references_kaz'];
		 $object->code_udk=$request['code_udk'];
		 $object->type_id=$request['type_id'];
		 $object->journal_id=$request['journal_id'];



		 $object->year=$request['year'];
		 //$object->month=$request[''];
		 //$object->day=$request[''];
		 $object->number=$request['number'];
		 $object->volume=$request['volume'];
		 $object->issue=$request['issue'];
		 $object->p_first=$request['p_first'];
		 $object->p_last=$request['p_last'];
		 //$object->pmid=$request[''];
		 //$object->user_id=$request[''];
		 //$object->conference_id=$request[''];
		 //$object->izdatelstvo=$request[''];
		 $object->user=$request['user'];
		 */
		/* foreign keys (arrays) */

		$authors_last_names = array();
		if(isset($request['c07_authors_lastname'])){
			$authors_ids = isset($request['c07_authors_id']) ? $request['c07_authors_id'] : null ;
			$authors_l_names = $request['c07_authors_lastname'];
			$authors_f_names = $request['c07_authors_firstname'];
			$authors_p_names = $request['c07_authors_patrname'];
			$authors_places_work = $request['c08_place_working_authors'];

			//["c07_authors_me"]=>
			//exit("What about c07_authors_me?");
			if(count($authors_l_names) > 1 ||  (count($authors_l_names) == 1 && trim($authors_l_names[0]) != "")){

				for ($i = 0; $i < count($authors_l_names); $i++) {
					$author = new Author();
					$author->publication_id = $object->id != 0 ? $object->id : null;
					if($authors_ids != null && isset($authors_ids[$i])){
						$author->id =  $authors_ids[$i];
					}
					$author->last_name = $authors_l_names[$i];
					$author->first_name = $authors_f_names[$i];
					$author->patronymic_name = $authors_p_names[$i];
					$author->organization_name = $authors_places_work[$i];
					$object->authors_array[] = $author;
				}
			}

		}

		if(isset($request['c19_list_references_kazakh'])){
			$reference_ids = isset($request['c19_list_references_id']) ? $request['c19_list_references_id'] : null ;
			$reference_kaz_type_ids = $request['reference_kaz_type_id'];
			$references_kazakh_names = $request['c19_list_references_kazakh'];

			if(count($references_kazakh_names) > 1 ||  (count($references_kazakh_names) == 1 && trim($references_kazakh_names[0]) != "")){


				for ($i = 0; $i < count($reference_kaz_type_ids); $i++) {
					$ref_kaz = new Reference();
					$ref_kaz->publication_id = $object->id != 0 ? $object->id : null;
					if($reference_ids != null && isset($reference_ids[$i])){
						$ref_kaz->id =  $reference_ids[$i];
					}
					$ref_kaz->type_id = $reference_kaz_type_ids[$i];
					$ref_kaz->name = $references_kazakh_names[$i];
					$object->references_array[] = $ref_kaz;
				}
					
			}

		}
	}
}


class ParseTezis extends ParseForm{

	public function __construct($request){
		parent::__construct($request);
		//$this->request=$request;
	}

	public function parse(){
		exit("ParseTezis deprecated");
		$this->parsedObject = new Publication();
		$object = $this->parsedObject;
		$request=$this->request;

		$object->id=$request['id'];
		$object->name=$request['name'];

		$object->type_id=$request['type_id'];

		$object->p_first=$request['p_first'];
		$object->p_last=$request['p_last'];

		$object->conference_name=$request['conference_name'];
		$object->conference_city=$request['conference_сity'];
		$object->conference_country=$request['conference_country'];
		$object->conference_type_id=$request['conference_type_id'];
		$object->conference_level_id=$request['conference_level_id'];
		$object->conference_type_pub_id=$request['conference_type_pub_id'];
		$object->conference_date_start=getDateFromFormatDate($request['conference_date_start']);
		$object->conference_date_finish=getDateFromFormatDate($request['conference_date_finish']);

		//echo "<h1>" . $request['conference_date_start'] . "</h1>";
		//var_dump($object);



		$object->user=$request['user'];

		/* foreign keys (arrays) */

		$authors_last_names = array();
		if(isset($request['c07_authors_lastname'])){
			$authors_ids = isset($request['c07_authors_id']) ? $request['c07_authors_id'] : null ;
			$authors_l_names = $request['c07_authors_lastname'];
			$authors_f_names = $request['c07_authors_firstname'];
			$authors_p_names = $request['c07_authors_patrname'];
			$authors_places_work = $request['c08_place_working_authors'];

			//["c07_authors_me"]=>
			//exit("What about c07_authors_me?");
			if(count($authors_l_names) > 1 ||  (count($authors_l_names) == 1 && trim($authors_l_names[0]) != "")){

				for ($i = 0; $i < count($authors_l_names); $i++) {
					$author = new Author();
					$author->publication_id = $object->id != 0 ? $object->id : null;
					if($authors_ids != null && isset($authors_ids[$i])){
						$author->id =  $authors_ids[$i];
					}
					$author->last_name = $authors_l_names[$i];
					$author->first_name = $authors_f_names[$i];
					$author->patronymic_name = $authors_p_names[$i];
					$author->organization_name = $authors_places_work[$i];
					$object->authors_array[] = $author;
				}
			}
		}
	}
}

class ParsePatent extends ParseForm{

	public function __construct($request){
		parent::__construct($request);
		//$this->request=$request;
	}

	public function parse(){
		exit("ParsePatent deprecated");
		$this->parsedObject = new Publication();
		$object = $this->parsedObject;
		$request=$this->request;

		$object->id=$request['id'];
		$object->name=$request['name'];

		$object->type_id=$request['type_id'];

		$object->patent_type_id=$request['patent_type_id'];
		$object->patent_type_number=$request['patent_type_number'];
		$object->patent_date=getDateFromFormatDate($request['patent_date']);

		$object->user=$request['user'];

		/* foreign keys (arrays) */

		$authors_last_names = array();
		if(isset($request['c07_authors_lastname'])){
			$authors_ids = isset($request['c07_authors_id']) ? $request['c07_authors_id'] : null ;
			$authors_l_names = $request['c07_authors_lastname'];
			$authors_f_names = $request['c07_authors_firstname'];
			$authors_p_names = $request['c07_authors_patrname'];
			$authors_places_work = $request['c08_place_working_authors'];

			//["c07_authors_me"]=>
			//exit("What about c07_authors_me?");
			if(count($authors_l_names) > 1 ||  (count($authors_l_names) == 1 && trim($authors_l_names[0]) != "")){

				for ($i = 0; $i < count($authors_l_names); $i++) {
					$author = new Author();
					$author->publication_id = $object->id != 0 ? $object->id : null;
					if($authors_ids != null && isset($authors_ids[$i])){
						$author->id =  $authors_ids[$i];
					}
					$author->last_name = $authors_l_names[$i];
					$author->first_name = $authors_f_names[$i];
					$author->patronymic_name = $authors_p_names[$i];
					$author->organization_name = $authors_places_work[$i];
					$object->authors_array[] = $author;
				}
			}
		}
	}
}


class ParseBook extends ParseForm{

	public function __construct($request){
		parent::__construct($request);
		//$this->request=$request;
	}

	public function parse(){
		$this->parsedObject = new Publication();
		$object = $this->parsedObject;
		$request=$this->request;

		$object->id=$request['id'];
		$object->name=$request['name'];

		$object->type_id=$request['type_id'];

		$object->year=$request['year'];
		$object->book_city=$request['book_city'];
		$object->book_pages=$request['book_pages'];
		$object->izdatelstvo=$request['izdatelstvo'];


		$object->code_udk=$request['code_udk'];
		$object->method_recom_bbk=$request['method_recom_bbk'];
		$object->journal_issn=$request['journal_issn'];
		$object->isbn=$request['isbn'];
		$object->method_recom_edited=$request['method_recom_edited'];
		$object->method_recom_stated=$request['method_recom_stated'];
		$object->method_recom_approved=$request['method_recom_approved'];
		$object->method_recom_published_with_the_support=$request['method_recom_published_with_the_support'];
		$object->abstract_original=$request['abstract_original'];
		$object->method_recom_reviewers=$request['method_recom_reviewers'];
		$object->number_tables=$request['number_tables'];
		$object->number_ilustrations=$request['number_ilustrations'];





		$object->user=$request['user'];

		/* foreign keys (arrays) */

		$authors_last_names = array();
		if(isset($request['c07_authors_lastname'])){
			$authors_ids = isset($request['c07_authors_id']) ? $request['c07_authors_id'] : null ;
			$authors_l_names = $request['c07_authors_lastname'];
			$authors_f_names = $request['c07_authors_firstname'];
			$authors_p_names = $request['c07_authors_patrname'];
			$authors_places_work = $request['c08_place_working_authors'];

			//["c07_authors_me"]=>
			//exit("What about c07_authors_me?");
			if(count($authors_l_names) > 1 ||  (count($authors_l_names) == 1 && trim($authors_l_names[0]) != "")){

				for ($i = 0; $i < count($authors_l_names); $i++) {
					$author = new Author();
					$author->publication_id = $object->id != 0 ? $object->id : null;
					if($authors_ids != null && isset($authors_ids[$i])){
						$author->id =  $authors_ids[$i];
					}
					$author->last_name = $authors_l_names[$i];
					$author->first_name = $authors_f_names[$i];
					$author->patronymic_name = $authors_p_names[$i];
					$author->organization_name = $authors_places_work[$i];
					$object->authors_array[] = $author;
				}
			}
		}
	}
}

class ParseUser extends ParseForm{



	public function __construct($request){
		parent::__construct($request);
		//$this->request=$request;
	}

	public function parse(){
		$this->parsedObject = new User();
		$object = $this->parsedObject;
		$request=$this->request;

		$object->id=$request['id'];
		$object->username_email=$request['username_email'];
		$object->password=$request['password'];
		$object->last_name=$request['last_name'];
		$object->first_name=$request['first_name'];
		$object->patronymic_name=$request['patronymic_name'];
		$object->last_name_en=$request['last_name_en'];
		$object->first_name_en=$request['first_name_en'];
		$object->patronymic_name_en=$request['patronymic_name_en'];
		$object->departament=$request['departament'];
		$object->status=$request['status'];
		$object->sex_id=$request['sex_id'];
		//$object->date_birth=$request['date_birth'];
		//$object->project=$request['project'];
		//$object->comments=$request['comments'];

	}
}

?>
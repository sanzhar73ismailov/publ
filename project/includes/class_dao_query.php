<?php

class FabricaQuery{

	public static function createQuery($pdo, $object){
		$query = null;

		switch(get_class($object)){
			case "Publication":
				$query = new PublicationQuery($pdo, $object);
				break;

			case "Author":
				$query = new AuthorQuery($pdo, $object);
				break;

			case "Reference":
				$query = new ReferenceQuery($pdo, $object);
				break;

			case "User":
				$query = new UserQuery($pdo, $object);
				break;
					
			case "PublicationUser":
				$query = new PublicationUserQuery($pdo, $object);
				break;

			case "Journal":
				$query = new JournalQuery($pdo, $object);
				break;

			case "Conference":
				$query = new ConferenceQuery($pdo, $object);
				break;
				
			case "DicIdName":
				$query = new DictionaryQuery($pdo, $object);
				break;
					

			default:
				exit("Unsupported object - see FabricaQuery::createQuery()");
				break;
		}

		return $query;

	}

}

abstract class DaoQuery{

	protected  $pdo;
	protected $object;
	protected $table;

	public function __construct($pdo, $object){
		$this->pdo = $pdo;
		$this->object = $object;
	}

	public abstract function insertQuery();

	public abstract  function updateQuery($by_column=null);

	public  function selectQueryAll($order=null){

		$returnObjects = array();
		$query =  "SELECT *	FROM `" . $this->table . "` p ";
		if($order != null){
			$query .= $query . $order;
		}

		//echo $query . "<br>";

		try {
			$stmt = $this->pdo->prepare($query);



			//$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		if(count($rows) == 0){
			return null;
		}

		$returnObjects = $this->fromRowsToArrayObjects($rows);


		//var_dump($returnObjects);

		return $returnObjects;

			
	}

	public  function selectQueryManyByCondition($conditionArray, $order=null){
		$returnObjects = array();
		$rows = $this->forSelectQueryManyByCondition($this->table, $conditionArray, $order);
		$returnObjects = $this->fromRowsToArrayObjects($rows);
		return $returnObjects;
	}

	public  function selectQueryOneById(){
		$rows = array();
		$retObjs = array();
		$query =  "SELECT * FROM `" . $this->table . "`  WHERE id = :id";
		try {
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}

		if(count($rows) == 0){
			return null;
		}
		$retObjs = $this->fromRowsToArrayObjects($rows);

		return $retObjs[0];

	}

	public  function selectQueryNative($query){
		$returnObjects = array();
		$rows = array();

		try {
			$stmt = $this->pdo->prepare($query);
			//$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		if(count($rows) == 0){
			return $returnObjects;
		}

		$returnObjects = $this->fromRowsToArrayObjects($rows);
		//var_dump($returnObjects);

		return $returnObjects;
	}
	public abstract  function fromRowsToArrayObjects($rows);
	protected  function forSelectQueryManyByCondition( $table, $conditionArray, $order="id"){

		$returnObjects = array();
		$rows = array();
		$query =  "SELECT * FROM " .$table . " t  WHERE 1=1 ";
		foreach ($conditionArray as $condition_object){

			$query .= " AND " . $condition_object->column ." " . $condition_object->operator . " :" .  $condition_object->column;

		}
		$query .=  " order by " . $order;

		//echo $query . "<br>";

		try {
			$stmt = $this->pdo->prepare($query);

			foreach ($conditionArray as $condition_object){
				if($condition_object->type == "str"){
					$stmt->bindValue(":" .  $condition_object->column,  $condition_object->value, PDO::PARAM_STR);

				}elseif($condition_object->type == "int"){
					$stmt->bindValue(":" .  $condition_object->column,  $condition_object->value, PDO::PARAM_INT);
				}
			}

			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		//if(count($rows) == 0){
		//	return null;
		//}
		return $rows;
	}
	public function deleteQuery(){
		return $this->forDeleteQuery();
	}
	protected  function forDeleteQuery(){
		$query = "DELETE FROM `" . $this->table ."`	WHERE `id` = :id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_STR);
		try {
			$stmt->execute();
			$affected_rows = $stmt->rowCount();
			if($affected_rows < 1){
				die("Ошибка, объект не удален");
			}
		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $affected_rows;
	}
	public abstract function bindValue(&$stmt);
}

class PublicationQuery extends DaoQuery{


	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_publication';
	}

	public function insertQuery(){
		$insetIdOfPublication = $this->insertOnlyPublication();

		if($this->object->authors_array != null){

			foreach ($this->object->authors_array as $key=>$entity){
				$entity->publication_id = $insetIdOfPublication;
				$daoQuery = FabricaQuery::createQuery($this->pdo, $entity);
				$entity->user = $this->object->user;
				$daoQuery->insertQuery();
			}
		}

		if($this->object->references_array != null){
			foreach ($this->object->references_array as $key=>$entity){
				$entity->publication_id = $insetIdOfPublication;
				$daoQuery = FabricaQuery::createQuery($this->pdo, $entity);
				$entity->user = $this->object->user;
				$daoQuery->insertQuery();
			}

		}

		$publUserObj = new PublicationUser();
		$publUserObj->publication_id = $insetIdOfPublication;
		$publUserObj->user_id = $_SESSION['user']['id'];
		$publUserObj->user_id = $_SESSION['user']['id'];
		$publUserObj->responsible = 1;
		$publUserObj->coauthor = $this->object->coauthor;

		$daoQuery = FabricaQuery::createQuery($this->pdo, $publUserObj);
		$daoQuery->insertQuery();

		return $insetIdOfPublication;
	}

	public function bindValue(&$stmt){
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':name', $this->object->name, PDO::PARAM_STR);
		$stmt->bindValue(':electron', $this->object->electron, PDO::PARAM_STR);
		$stmt->bindValue(':url', $this->object->url, PDO::PARAM_STR);
		$stmt->bindValue(':doi', $this->object->doi, PDO::PARAM_STR);
		$stmt->bindValue(':abstract_original', $this->object->abstract_original, PDO::PARAM_STR);
		$stmt->bindValue(':abstract_rus', $this->object->abstract_rus, PDO::PARAM_STR);
		$stmt->bindValue(':abstract_kaz', $this->object->abstract_kaz, PDO::PARAM_STR);
		$stmt->bindValue(':abstract_eng', $this->object->abstract_eng, PDO::PARAM_STR);
		$stmt->bindValue(':language', $this->object->language, PDO::PARAM_STR);
		$stmt->bindValue(':keywords', $this->object->keywords, PDO::PARAM_STR);
		$stmt->bindValue(':number_ilustrations', $this->object->number_ilustrations, PDO::PARAM_STR);
		$stmt->bindValue(':number_tables', $this->object->number_tables, PDO::PARAM_STR);
		$stmt->bindValue(':number_references', $this->object->number_references, PDO::PARAM_STR);
		$stmt->bindValue(':number_references_kaz', $this->object->number_references_kaz, PDO::PARAM_STR);
		$stmt->bindValue(':code_udk', $this->object->code_udk, PDO::PARAM_STR);
		$stmt->bindValue(':type_id', $this->object->type_id, PDO::PARAM_STR);
		$stmt->bindValue(':journal_id', $this->object->journal_id, PDO::PARAM_STR);
		//$stmt->bindValue(':journal_name', $this->object->journal_name, PDO::PARAM_STR);
		//$stmt->bindValue(':journal_country', $this->object->journal_country, PDO::PARAM_STR);
		//$stmt->bindValue(':journal_issn', $this->object->journal_issn, PDO::PARAM_STR);
		//$stmt->bindValue(':journal_periodicity', $this->object->journal_periodicity, PDO::PARAM_STR);
		//$stmt->bindValue(':journal_izdatelstvo_mesto_izdaniya', $this->object->journal_izdatelstvo_mesto_izdaniya, PDO::PARAM_STR);
		$stmt->bindValue(':year', $this->object->year, PDO::PARAM_STR);
		$stmt->bindValue(':month', $this->object->month, PDO::PARAM_STR);
		$stmt->bindValue(':day', $this->object->day, PDO::PARAM_STR);
		$stmt->bindValue(':number', $this->object->number, PDO::PARAM_STR);
		$stmt->bindValue(':volume', $this->object->volume, PDO::PARAM_STR);
		$stmt->bindValue(':issue', $this->object->issue, PDO::PARAM_STR);
		$stmt->bindValue(':p_first', $this->object->p_first, PDO::PARAM_STR);
		$stmt->bindValue(':p_last', $this->object->p_last, PDO::PARAM_STR);
		$stmt->bindValue(':pmid', $this->object->pmid, PDO::PARAM_STR);
		$stmt->bindValue(':conference_id', $this->object->conference_id, PDO::PARAM_STR);
		$stmt->bindValue(':tezis_type', $this->object->tezis_type, PDO::PARAM_STR);

		//$stmt->bindValue(':conference_name', $this->object->conference_name, PDO::PARAM_STR);
		//$stmt->bindValue(':conference_city', $this->object->conference_city, PDO::PARAM_STR);
		//$stmt->bindValue(':conference_country', $this->object->conference_country, PDO::PARAM_STR);
		//$stmt->bindValue(':conference_type_id', $this->object->conference_type_id, PDO::PARAM_STR);
		//$stmt->bindValue(':conference_level_id', $this->object->conference_level_id, PDO::PARAM_STR);

		//$stmt->bindValue(':conference_type_pub_id', $this->object->conference_type_pub_id, PDO::PARAM_STR);
		//$stmt->bindValue(':conference_date_start', getSqlDateFromDate($this->object->conference_date_start), PDO::PARAM_STR);
		//$stmt->bindValue(':conference_date_finish', getSqlDateFromDate($this->object->conference_date_finish), PDO::PARAM_STR);


		$stmt->bindValue(':patent_type_id', $this->object->patent_type_id, PDO::PARAM_STR);
		$stmt->bindValue(':patent_type_number', $this->object->patent_type_number, PDO::PARAM_STR);
		$stmt->bindValue(':patent_date',  getSqlDateFromDate($this->object->patent_date), PDO::PARAM_STR);
		$stmt->bindValue(':book_city', $this->object->book_city, PDO::PARAM_STR);
		$stmt->bindValue(':book_pages', $this->object->book_pages, PDO::PARAM_STR);
		$stmt->bindValue(':izdatelstvo', $this->object->izdatelstvo, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);


		$stmt->bindValue(':method_recom_bbk', $this->object->method_recom_bbk, PDO::PARAM_STR);
		$stmt->bindValue(':isbn', $this->object->isbn, PDO::PARAM_STR);
		$stmt->bindValue(':method_recom_edited', $this->object->method_recom_edited, PDO::PARAM_STR);
		$stmt->bindValue(':method_recom_stated', $this->object->method_recom_stated, PDO::PARAM_STR);
		$stmt->bindValue(':method_recom_approved', $this->object->method_recom_approved, PDO::PARAM_STR);
		$stmt->bindValue(':method_recom_published_with_the_support', $this->object->method_recom_published_with_the_support, PDO::PARAM_STR);
		$stmt->bindValue(':method_recom_reviewers', $this->object->method_recom_reviewers, PDO::PARAM_STR);



	}

	private  function insertOnlyPublication(){
		$query = "INSERT INTO
				  `" . $this->table . "`
						(
						  `id`,
						  `name`,
						  `electron`,
						  `url`,
						  `doi`,
						  `abstract_original`,
						  `abstract_rus`,
						  `abstract_kaz`,
						  `abstract_eng`,
						  `language`,
						  `keywords`,
						  `number_ilustrations`,
						  `number_tables`,
						  `number_references`,
						  `number_references_kaz`,
						  `code_udk`,
						  `type_id`,
						  `journal_id`,
						   `year`,
						  `month`,
						  `day`,
						  `number`,
						  `volume`,
						  `issue`,
						  `p_first`,
						  `p_last`,
						  `pmid`,
						  `conference_id`,
						  `tezis_type`,
						  `patent_type_id`,
						  `patent_type_number`,
						  `patent_date`,
						  `book_city`,
						  `book_pages`,
						  `izdatelstvo`,
						  `user`,
						  `method_recom_bbk`,
						  `isbn`,
						  `method_recom_edited`,
						  `method_recom_stated`,
						  `method_recom_approved`,
						  `method_recom_published_with_the_support`,
						  `method_recom_reviewers`
						) 
						VALUE (
						  :id,
						  :name,
						  :electron,
						  :url,
						  :doi,
						  :abstract_original,
						  :abstract_rus,
						  :abstract_kaz,
						  :abstract_eng,
						  :language,
						  :keywords,
						  :number_ilustrations,
						  :number_tables,
						  :number_references,
						  :number_references_kaz,
						  :code_udk,
						  :type_id,
						  :journal_id,
						  :year,
						  :month,
						  :day,
						  :number,
						  :volume,
						  :issue,
						  :p_first,
						  :p_last,
						  :pmid,
						  :conference_id,
						  :tezis_type,
						  :patent_type_id,
						  :patent_type_number,
						  :patent_date,
						  :book_city,
						  :book_pages,
						  :izdatelstvo,
						  :user,
						  :method_recom_bbk,
						  :isbn,
						  :method_recom_edited,
						  :method_recom_stated,
						  :method_recom_approved,
						  :method_recom_published_with_the_support,
						  :method_recom_reviewers
						)";

		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);


		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();

	}

	public  function updateQuery($by_column=null){
		$this->updateOnlyPublication();
		$this->updateAlsoForeignTables($this->object->authors_array, new Author());
		$this->updateAlsoForeignTables($this->object->references_array, new Reference());

		
		$publUserObj = new PublicationUser();
		$publUserObj->publication_id =  $this->object->id;
		$publUserObj->coauthor = $this->object->coauthor;
		
		//todo

		$daoQuery = FabricaQuery::createQuery($this->pdo, $publUserObj);
		$daoQuery->updateCoauthorColumnByPublicationId();


		return $this->object->id;

	}

	private function getNewObject($foreignEntity){
		$object = null;
		if(get_class($foreignEntity) == "Author"){
			$object = new Author();
		}elseif (get_class($foreignEntity) == "Reference"){
			$object = new Reference();
		}
		return $object;
	}

	private function updateAlsoForeignTables($fieldArray,$foreignEntity){
		// 1) смотрим все ли авторы имеют id, если все имеют значит ничего инсертить не надо.
		//    если есть без id, из них создаем массив для инсерта и publication_id данного объекта
		$arrayAuthorsForInsert = array(); // массив авторов, которых надо добавить
		$arrayAuthorObjInFormToUpdate= array(); // массив авторов, которых надо обновить
		$arrayAuthorIdInFormToUpdate = array(); // массив id (айдишников_ авторов), которых надо обновить, нужно для того чтобы вычисчить тех которых удалить надо
		$arrayAuthorIdInDb = array(); //массив id (айдишников_ авторов), в базе по этой публикации
		$arrayAuthorIdToDelete = array(); //массив id (айдишников_ авторов), которых надо удалить


		//echo "<br>" . "Новые авторы:" . "<br>";
		foreach ($fieldArray as $key=>$entity){
			$entity->publication_id = $this->object->id;
			if($entity->id == null){
				$arrayAuthorsForInsert[] = $entity;
				//	echo "<br>" . "Новые объект:" . $entity . "<br>";

			}else{
				$arrayAuthorObjInFormToUpdate [] = $entity;
				$arrayAuthorIdInFormToUpdate[] = $entity->id;
				//echo "<br>" . "Старый объект:" . $entity . "<br>";
			}
		}



		// 2) вытаскиваем всех авторов по этой публикации и сравниваем с тем что пришло с формы.
		//    если все без изменений, то просто все записи обновляем

		$authorQuery =FabricaQuery::createQuery($this->pdo, $this->getNewObject($foreignEntity));

		$conditionArray = array(new QueryCondition("publication_id", $this->object->id));
		$arrayAuthorsFromDb = $authorQuery->selectQueryManyByCondition($conditionArray, "id");
		//echo "<br>---------------<br>";
		foreach ($arrayAuthorsFromDb as $key=>$entity){
			$arrayAuthorIdInDb[] = $entity->id;
			//echo "<br>" . "Автор в БД :" . $entity . "<br>";
		}


		// вытаскиваем те id которые в базе лишние
		$arrayAuthorIdToDelete = array_diff($arrayAuthorIdInDb, $arrayAuthorIdInFormToUpdate);

		//echo "<br>-------arrayAuthorIdInFormToUpdate--------<br>";
		//var_dump($arrayAuthorIdInFormToUpdate);

		//echo "<br>-------arrayAuthorObjInFormToUpdate--------<br>";
		//var_dump($arrayAuthorObjInFormToUpdate);

		//echo "<br>-------arrayAuthorIdInDb--------<br>";
		//var_dump($arrayAuthorIdInDb);

		//echo "<br>---------------<br>";
		//var_dump($arrayAuthorIdToDelete);

		//удаляем их из базы
		foreach ($arrayAuthorIdToDelete as $key=>$id_to_delete){
			$entity = $this->getNewObject($foreignEntity);
			$entity->id=$id_to_delete;
			$daoQuery = FabricaQuery::createQuery($this->pdo, $entity);
			$daoQuery->deleteQuery();
		}

		//обновляем в базе из формы, тех что с id
		foreach ($arrayAuthorObjInFormToUpdate as $key=>$entity){
			//echo "<br>---------------<br>";
			//var_dump($entity);
			$daoQuery = FabricaQuery::createQuery($this->pdo, $entity);
			$entity->user = $this->object->user;
			$daoQuery->updateQuery();
		}

		//инсертим если таковые есть
		foreach ($arrayAuthorsForInsert as $key=>$entity){
			$daoQuery = FabricaQuery::createQuery($this->pdo, $entity);
			$entity->user = $this->object->user;
			$daoQuery->insertQuery();
		}
	}

	public  function updateOnlyPublication($by_column=null){
		$query = "UPDATE
				  `" . $this->table . "`  
								SET 
				  `name` = :name,
				  `electron` = :electron,
				  `url` = :url,
				  `doi` = :doi,
				  `abstract_original` = :abstract_original,
				  `abstract_rus` = :abstract_rus,
				  `abstract_kaz` = :abstract_kaz,
				  `abstract_eng` = :abstract_eng,
				  `language` = :language,
				  `keywords` = :keywords,
				  `number_ilustrations` = :number_ilustrations,
				  `number_tables` = :number_tables,
				  `number_references` = :number_references,
				  `number_references_kaz` = :number_references_kaz,
				  `code_udk` = :code_udk,
				  `type_id` = :type_id,
				  `journal_id` = :journal_id,
				  `year` = :year,
				  `month` = :month,
				  `day` = :day,
				  `number` = :number,
				  `volume` = :volume,
				  `issue` = :issue,
				  `p_first` = :p_first,
				  `p_last` = :p_last,
				  `pmid` = :pmid,
				  `conference_id` = :conference_id,
				  `tezis_type` = :tezis_type,
				  `patent_type_id` = :patent_type_id,
				  `patent_type_number` = :patent_type_number,
				  `patent_date` = :patent_date,
				  `book_city` = :book_city,
				  `book_pages` = :book_pages,
				  `izdatelstvo` = :izdatelstvo,
				  `user` = :user,
				   method_recom_bbk=:method_recom_bbk,
				   isbn=:isbn,
				   method_recom_edited=:method_recom_edited,
				   method_recom_stated=:method_recom_stated,
				   method_recom_approved=:method_recom_approved,
				   method_recom_published_with_the_support=:method_recom_published_with_the_support,
				   method_recom_reviewers=:method_recom_reviewers
				WHERE 
				  `id` = :id";

		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);

		//echo "<br>".$stmt->queryString . "<br>";
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
			

		return $this->object->id;

	}

	private function getDicValues($object){
		$queryArr = FabricaQuery::createQuery($this->pdo, $object);
		$conditionArray = array(new QueryCondition("publication_id", $this->object->id));
		return $queryArr->selectQueryManyByCondition($conditionArray, "id");
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();
		foreach ($rows as $row){
			$object = new Publication();
			$object->id=$row['id'];
			$object->name=$row['name'];
			$object->electron=$row['electron'];
			$object->url=$row['url'];
			$object->doi=$row['doi'];
			$object->abstract_original=$row['abstract_original'];
			$object->abstract_rus=$row['abstract_rus'];
			$object->abstract_kaz=$row['abstract_kaz'];
			$object->abstract_eng=$row['abstract_eng'];
			$object->language=$row['language'];
			$object->keywords=$row['keywords'];
			$object->number_ilustrations=$row['number_ilustrations'];
			$object->number_tables=$row['number_tables'];
			$object->number_references=$row['number_references'];
			$object->number_references_kaz=$row['number_references_kaz'];
			$object->code_udk=$row['code_udk'];
			$object->type_id=$row['type_id'];
			$object->journal_id=$row['journal_id'];
			$object->year=$row['year'];
			$object->month=$row['month'];
			$object->day=$row['day'];
			$object->number=$row['number'];
			$object->volume=$row['volume'];
			$object->issue=$row['issue'];
			$object->p_first=$row['p_first'];
			$object->p_last=$row['p_last'];
			$object->pmid=$row['pmid'];

			//$object->conference_id=$row['conference_id'];

			$object->conference_id=$row['conference_id'];
			$object->tezis_type=$row['tezis_type'];


			$object->patent_type_id=$row['patent_type_id'];
			$object->patent_type_number=$row['patent_type_number'];
			$object->patent_date=getDateFromSqlDate($row['patent_date']);

			$object->book_city=$row['book_city'];
			$object->book_pages=$row['book_pages'];
			$object->izdatelstvo=$row['izdatelstvo'];

			$object->user=$row['user'];
			$object->insert_date=$row['insert_date'];


			$object->method_recom_bbk=$row['method_recom_bbk'];
			$object->isbn=$row['isbn'];
			$object->method_recom_edited=$row['method_recom_edited'];
			$object->method_recom_stated=$row['method_recom_stated'];
			$object->method_recom_approved=$row['method_recom_approved'];
			$object->method_recom_published_with_the_support=$row['method_recom_published_with_the_support'];
			$object->method_recom_reviewers=$row['method_recom_reviewers'];
			
			if(isset($row['responsible'])){
				$object->responsible = $row['responsible'];
			}
			
		    if(isset($row['coauthor'])){
				$object->coauthor = $row['coauthor'];
			}
		    
			if(isset($row['user_responsible'])){
				$object->user_responsible = $row['user_responsible'];
			}



			$daoQuery = FabricaQuery::createQuery($this->pdo, new Author());
			$condArray = array(new QueryCondition("publication_id", $object->id));
			$object->authors_array = $daoQuery->selectQueryManyByCondition($condArray, "id");

			$daoQuery = FabricaQuery::createQuery($this->pdo, new Reference());
			$condArray = array(new QueryCondition("publication_id", $object->id));
			$object->references_array = $daoQuery->selectQueryManyByCondition($condArray, "id");



			$returnObjects[] = $object;
		}
		return $returnObjects;
	}




}

class AuthorQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_author';
	}

	public  function insertQuery(){
		$query = "INSERT INTO
			  `" . $this->table. "`
			(
			  `id`,
			  `publication_id`,
			  `last_name`,
			  `first_name`,
			  `patronymic_name`,
			  `organization_name`,
			  `organization_id`,
			  `user`
			) 
			VALUE (
			  :id,
			  :publication_id,
			  :last_name,
			  :first_name,
			  :patronymic_name,
			  :organization_name,
			  :organization_id,
			  :user
			)";



		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':publication_id', $this->object->publication_id, PDO::PARAM_STR);
		$stmt->bindValue(':last_name', $this->object->last_name, PDO::PARAM_STR);
		$stmt->bindValue(':first_name', $this->object->first_name, PDO::PARAM_STR);
		$stmt->bindValue(':patronymic_name', $this->object->patronymic_name, PDO::PARAM_STR);
		$stmt->bindValue(':organization_name', $this->object->organization_name, PDO::PARAM_STR);
		$stmt->bindValue(':organization_id', $this->object->organization_id, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);

		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		$query = "UPDATE
					  `" . $this->table . "`  
					SET 
					  `publication_id` = :publication_id,
					  `last_name` = :last_name,
					  `first_name` = :first_name,
					  `patronymic_name` = :patronymic_name,
					  `organization_name` = :organization_name,
					  `organization_id` = :organization_id,
					  `user` = :user
					WHERE 
					  `id` = :id
				";

		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':publication_id', $this->object->publication_id, PDO::PARAM_STR);
		$stmt->bindValue(':last_name', $this->object->last_name, PDO::PARAM_STR);
		$stmt->bindValue(':first_name', $this->object->first_name, PDO::PARAM_STR);
		$stmt->bindValue(':patronymic_name', $this->object->patronymic_name, PDO::PARAM_STR);
		$stmt->bindValue(':organization_name', $this->object->organization_name, PDO::PARAM_STR);
		$stmt->bindValue(':organization_id', $this->object->organization_id, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);

		//echo "<br>".$stmt->queryString . "<br>";
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->object->id;
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();


		if($rows != null){
			foreach ($rows as $row){
				$object = new Author();
				$object->id=$row['id'];
				$object->publication_id=$row['publication_id'];
				$object->last_name=$row['last_name'];
				$object->first_name=$row['first_name'];
				$object->patronymic_name=$row['patronymic_name'];
				$object->organization_name=$row['organization_name'];
				$object->organization_id=$row['organization_id'];
				$object->user=$row['user'];
				$object->insert_date=$row['insert_date'];
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}

	public function bindValue(&$stmt){
		exit("UnsupportedOperation");
	}
}

class ReferenceQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_reference';
	}
	public  function insertQuery(){
		$query = "INSERT INTO
				  `". $this->table . "`
				(
				  `id`,
				  `publication_id`,
				  `type_id`,
				  `name`,
				  `user`,
				  `insert_date`
				) 
				VALUE (
				  :id,
				  :publication_id,
				  :type_id,
				  :name,
				  :user,
				  :insert_date
				);";



		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':publication_id', $this->object->publication_id, PDO::PARAM_STR);
		$stmt->bindValue(':type_id', $this->object->type_id, PDO::PARAM_STR);
		$stmt->bindValue(':name', $this->object->name, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);
		$stmt->bindValue(':insert_date', $this->object->insert_date, PDO::PARAM_STR);
			
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		$query = "UPDATE
			  `". $this->table . "` 
			SET 
			  `publication_id` = :publication_id,
			  `type_id` = :type_id,
			  `name` = :name,
			  `user` = :user
			WHERE 
			  `id` = :id";


		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':publication_id', $this->object->publication_id, PDO::PARAM_STR);
		$stmt->bindValue(':type_id', $this->object->type_id, PDO::PARAM_STR);
		$stmt->bindValue(':name', $this->object->name, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);


		//echo "<br>".$stmt->queryString . "<br>";
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->object->id;
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();

		if($rows != null){
			foreach ($rows as $row){
				$object = new Reference();
				$object->id=$row['id'];
				$object->publication_id=$row['publication_id'];
				$object->type_id=$row['type_id'];
				$object->name=$row['name'];
				$object->user=$row['user'];
				$object->insert_date=$row['insert_date'];
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}
	public function bindValue(&$stmt){
		exit("UnsupportedOperation");
	}
}

class PublicationUserQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_publication_user';
	}
	public  function insertQuery(){
		$query = "INSERT INTO
				  `". $this->table . "`
				(
				  `id`,
				  `publication_id`,
				  `user_id`,
				  `responsible`,
				  `coauthor`
				) 
				VALUE (
				  :id,
				  :publication_id,
				  :user_id,
				  :responsible,
				  :coauthor
				);";



		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);

		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		exit("UnsupportedOperationException");
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();

		if($rows != null){
			foreach ($rows as $row){
				$object = new PublicationUser();
				$object->id=$row['id'];
				$object->publication_id=$row['publication_id'];
				$object->user_id=$row['user_id'];
				$object->responsible=$row['responsible'];
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}

	public function bindValue(&$stmt){
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':publication_id', $this->object->publication_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $this->object->user_id, PDO::PARAM_STR);
		$stmt->bindValue(':responsible', $this->object->responsible, PDO::PARAM_STR);
		$stmt->bindValue(':coauthor', $this->object->coauthor, PDO::PARAM_STR);
	}

	public function updateCoauthorColumnByPublicationId(){
			$query = "UPDATE
			  `". $this->table . "` 
			SET 
			  `coauthor` = :coauthor
			WHERE 
			  `publication_id` = :publication_id";

		$stmt = $this->pdo->prepare($query);
		
		$stmt->bindValue(':publication_id', $this->object->publication_id, PDO::PARAM_STR);
		$stmt->bindValue(':coauthor', $this->object->coauthor, PDO::PARAM_STR);
		

		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->object->id;
	}

}


class UserQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_user';
	}
	public  function insertQuery(){
		$query = "INSERT INTO
				  `". $this->table . "`
				(
				  `id`,
				  `username_email`,
				  `password`,
				  `active`,
				  `last_name`,
				  `first_name`,
				  `patronymic_name`,
				  `last_name_en`,
				  `first_name_en`,
				  `patronymic_name_en`,
				  `departament`,
				  `status`,
				  `sex_id`,
				  `date_birth`,
				  `project`,
				  `comments`,
				  `user`,
				  `insert_date`
				) 
				VALUE (
				  :id,
				  :username_email,
				  :password,
				  :active,
				  :last_name,
				  :first_name,
				  :patronymic_name,
				  :last_name_en,
				  :first_name_en,
				  :patronymic_name_en,
				  :departament,
				  :status,
				  :sex_id,
				  :date_birth,
				  :project,
				  :comments,
				  :user,
				  :insert_date
				)";



		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':username_email', $this->object->username_email, PDO::PARAM_STR);
		$stmt->bindValue(':password', $this->object->password, PDO::PARAM_STR);
		$stmt->bindValue(':active', $this->object->active, PDO::PARAM_STR);
		$stmt->bindValue(':last_name', $this->object->last_name, PDO::PARAM_STR);
		$stmt->bindValue(':first_name', $this->object->first_name, PDO::PARAM_STR);
		$stmt->bindValue(':patronymic_name', $this->object->patronymic_name, PDO::PARAM_STR);
		$stmt->bindValue(':last_name_en', $this->object->last_name_en, PDO::PARAM_STR);
		$stmt->bindValue(':first_name_en', $this->object->first_name_en, PDO::PARAM_STR);
		$stmt->bindValue(':patronymic_name_en', $this->object->patronymic_name_en, PDO::PARAM_STR);
		$stmt->bindValue(':departament', $this->object->departament, PDO::PARAM_STR);
		$stmt->bindValue(':status', $this->object->status, PDO::PARAM_STR);
		$stmt->bindValue(':sex_id', $this->object->sex_id, PDO::PARAM_STR);
		$stmt->bindValue(':date_birth', $this->object->date_birth, PDO::PARAM_STR);
		$stmt->bindValue(':project', $this->object->project, PDO::PARAM_STR);
		$stmt->bindValue(':comments', $this->object->comments, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);
		$stmt->bindValue(':insert_date', $this->object->insert_date, PDO::PARAM_STR);

			
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		exit("UnsupportedOperationException");
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();

		if($rows != null){
			foreach ($rows as $row){
				$object = new User();
				$object->id=$row['id'];
				$object->username_email=$row['username_email'];
				$object->password=$row['password'];
				$object->active=$row['active'];

				$object->last_name=$row['last_name'];
				$object->first_name=$row['first_name'];
				$object->patronymic_name=$row['patronymic_name'];

				$object->last_name_en=$row['last_name_en'];
				$object->first_name_en=$row['first_name_en'];
				$object->patronymic_name_en=$row['patronymic_name_en'];

				$object->departament=$row['departament'];
				$object->status=$row['status'];
				$object->sex_id=$row['sex_id'];
				$object->date_birth=$row['date_birth'];
				$object->project=$row['project'];
				$object->comments=$row['comments'];
				$object->role=$row['role'];
				$object->user=$row['user'];
				$object->insert_date=$row['insert_date'];
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}

	public function bindValue(&$stmt){
		exit("UnsupportedOperation");
	}

}

class JournalQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_journal';
	}
	public  function insertQuery(){
		$query = "INSERT INTO
				  `". $this->table . "`
				(
				  `id`,
				  `country`,
				  `issn`,
				  `name`,
				  `periodicity`,
				  `izdatelstvo_mesto_izdaniya`,
				  `user`,
				  `insert_date`
				) 
				VALUE (
				  :id,
				  :country,
				  :issn,
				  :name,
				  :periodicity,
				  :izdatelstvo_mesto_izdaniya,
				  :user,
				  :insert_date
				);";


		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);
			
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		$query = "UPDATE
			  `". $this->table . "` 
			SET 
			  `country` = :country,
			  `issn` = :issn,
			  `name` = :name,
			  `periodicity` = :periodicity,
			  `izdatelstvo_mesto_izdaniya` = :izdatelstvo_mesto_izdaniya,
			  `name` = :name,
			  `user` = :user
			WHERE 
			  `id` = :id";


		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);


		//echo "<br>".$stmt->queryString . "<br>";
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->object->id;
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();

		if($rows != null){
			foreach ($rows as $row){
				$object = new Journal();
				$object->id=$row['id'];
				$object->country=$row['country'];
				$object->issn=$row['issn'];
				$object->name=$row['name'];
				$object->periodicity=$row['periodicity'];
				$object->izdatelstvo_mesto_izdaniya=$row['izdatelstvo_mesto_izdaniya'];
				$object->user=$row['user'];
				$object->insert_date=$row['insert_date'];
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}
	public function bindValue(&$stmt){
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':country', $this->object->country, PDO::PARAM_STR);
		$stmt->bindValue(':issn', $this->object->issn, PDO::PARAM_STR);
		$stmt->bindValue(':name', $this->object->name, PDO::PARAM_STR);
		$stmt->bindValue(':periodicity', $this->object->periodicity, PDO::PARAM_STR);
		$stmt->bindValue(':izdatelstvo_mesto_izdaniya', $this->object->izdatelstvo_mesto_izdaniya, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);
		$stmt->bindValue(':insert_date', $this->object->insert_date, PDO::PARAM_STR);
	}

}
class ConferenceQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_conference';
	}
	public  function insertQuery(){
		$query = "INSERT INTO
				  `". $this->table . "`
				(
				  `id`,
				  `name`,
				  `sbornik_name`,
				  `city`,
				  `country`,
				  `type_id`,
				  `level_id`,
				  `date_start`,
				  `date_finish`,
				  `add_info`,
				  `user`,
				  `insert_date`
				) 
				VALUE (
				:id,
				:name,
				:sbornik_name,
				:city,
				:country,
				:type_id,
				:level_id,
				:date_start,
				:date_finish,
				:add_info,
				:user,
				:insert_date
				);";



		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);
			
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		$query = "UPDATE
			  `". $this->table . "` 
			SET 
			  `name` = :name,
			  `sbornik_name` = :sbornik_name,
			  `city` = :city,
			  `country` = :country,
			  `type_id` = :type_id,
			  `level_id` = :level_id,
			  `date_start` = :date_start,
			  `date_finish` = :date_finish,
			  `add_info` = :add_info,
			  `user` = :user
			 WHERE 
			  `id` = :id";



		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);


		//echo "<br>".$stmt->queryString . "<br>";
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->object->id;
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();

		if($rows != null){
			foreach ($rows as $row){
				$object = new Conference();
				$object->id=$row['id'];
				$object->name=$row['name'];
				$object->sbornik_name=$row['sbornik_name'];
				$object->city=$row['city'];
				$object->country=$row['country'];
				$object->type_id=$row['type_id'];
				$object->level_id=$row['level_id'];
				$object->date_start=getDateFromSqlDate($row['date_start']);
				$object->date_finish=getDateFromSqlDate($row['date_finish']);
				$object->add_info=$row['add_info'];
				$object->user=$row['user'];
				$object->insert_date=$row['insert_date'];
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}
	public function bindValue(&$stmt){
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':name', $this->object->name, PDO::PARAM_STR);
		$stmt->bindValue(':sbornik_name', $this->object->sbornik_name, PDO::PARAM_STR);
		$stmt->bindValue(':city', $this->object->city, PDO::PARAM_STR);
		$stmt->bindValue(':country', $this->object->country, PDO::PARAM_STR);
		$stmt->bindValue(':type_id', $this->object->type_id, PDO::PARAM_STR);
		$stmt->bindValue(':level_id', $this->object->level_id, PDO::PARAM_STR);
		$stmt->bindValue(':date_start', getSqlDateFromDate($this->object->date_start), PDO::PARAM_STR);
		$stmt->bindValue(':date_finish', getSqlDateFromDate($this->object->date_finish), PDO::PARAM_STR);
		$stmt->bindValue(':add_info', $this->object->add_info, PDO::PARAM_STR);
		$stmt->bindValue(':user', $this->object->user, PDO::PARAM_STR);
		$stmt->bindValue(':insert_date', $this->object->insert_date, PDO::PARAM_STR);
	}

}

class DictionaryQuery extends DaoQuery{

	public function __construct($pdo, $object){
		parent::__construct($pdo, $object);
		$this->table = 'bibl_' . $object->table_name;
	}
	public  function insertQuery(){
		$query = "INSERT INTO
				  `". $this->table . "`
				(
				  `id`,
				  `name`
				) 
				VALUE (
				:id,
				:name
				);";



		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);
			
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				die("Ошибка, объект не сохранен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->pdo->lastInsertId();
	}

	public  function updateQuery($by_column=null){
		$query = "UPDATE
			  `". $this->table . "` 
			SET 
			  `name` = :name
			 WHERE 
			  `id` = :id";



		$stmt = $this->pdo->prepare($query);
		$this->bindValue($stmt);


		//echo "<br>".$stmt->queryString . "<br>";
		try {
			$stmt->execute();

			$affected_rows = $stmt->rowCount();
			//	echo $affected_rows.' пациент добавлен';
			if($affected_rows < 1){
				//die("Ошибка, объект не обновлен");
			}

		} catch(PDOException $ex) {
			echo "Ошибка:" . $ex->getMessage();
		}
		return $this->object->id;
	}

	public function fromRowsToArrayObjects($rows){
		$returnObjects = array();

		if($rows != null){
			foreach ($rows as $row){
				$object = new DicIdName($row['id'], $row['name']);
				$returnObjects[] = $object;
			}
		}

		return $returnObjects;
	}
	public function bindValue(&$stmt){
		$stmt->bindValue(':id', $this->object->id, PDO::PARAM_INT);
		$stmt->bindValue(':name', $this->object->name, PDO::PARAM_STR);
	}

}
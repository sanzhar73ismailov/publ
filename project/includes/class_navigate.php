<?php



class FabricaNavigate{

	public  static function createNavigate($str_path, $session, $request, $doid){
		global $smarty;

		$navigate_obj = null;

		switch ($str_path){
			case 'add_publication':

				$navigate_obj = new AddPublNavigate($smarty, $session, $doid, $request);
				break;
					
			case 'publication':

				$navigate_obj = new PublicationNavigate($smarty, $session, $doid, $request);
				break;
			case 'contacts':

				$navigate_obj = new ContactNavigate($smarty, $session);
				break;
			case 'feedback':

				$navigate_obj = new FeedbackNavigate($smarty, $session);
				break;
			case 'register':

				$navigate_obj = new RegistrationNavigate($smarty, $session);
				break;
			case 'activate_account':

				$navigate_obj = new ActivationNavigate($smarty, $session);
				break;
			case 'login':

				$navigate_obj = new LoginNavigate($smarty, $session);
				break;
			case 'logout':

				$navigate_obj = new LogoutNavigate($smarty, $session);
				break;
			case 'list_abs_data':

				exit("Unsupported operation");
				break;
			case "list":

				$navigate_obj = new ListNavigate($smarty, $session, $request);
				break;

			case "list_possible":
				$navigate_obj = new ListPossibleNavigate($smarty, $session);
				break;

			case "save_possible":
				$navigate_obj = new SavePossibleNavigate($smarty, $session);
				break;

			default:

				$navigate_obj = new IndexNavigate($smarty, $session);
				break;
		}
		return $navigate_obj;

	}

}

abstract class AbstractNavigate{
	protected $title;
	protected $template;
	protected $smartyArray = array();
	protected $smarty;
	protected $session;
	protected $restricted = false;
	protected $authorized;
	protected $message = "";

	public function __construct($smarty, $session){
		$this->smarty = $smarty;
		$this->session = $session;

		//echo "<h1>_SESSION[authorized]" .$_SESSION["authorized"]."</h1>";


		if(!isset($_SESSION["authorized"]) || $_SESSION["authorized"] != 1){
			$this->authorized = false;
			//$this->authorized = true;
			$smarty->assign('authorized',0);
		}else{
			$this->authorized = true;
			$smarty->assign('authorized',1);
			$smarty->assign('user',$_SESSION['user']);
		}
	}


	public function display(){

		$this->init();

		$this->smarty->assign('title',$this->title);
		$this->smarty->assign('message',$this->message);

		foreach($this->smartyArray as $key => $value){
			$this->smarty->assign($key,$value);

		}


		if(!$this->restricted || ($this->restricted && $this->authorized)){
			$this->smarty->display('templates/' . $this->template);
		}else{
			$this->smarty->assign('title',"Вход");
			$this->smarty->assign('message',"Необходимио авторизоваться");
			$this->smarty->display('templates/login.tpl');
			exit;
		}
	}

	public abstract  function init();

}


class AddPublNavigate extends AbstractNavigate{
	private $do;
	private $id;
	private $type_publ;
	private $request;
	private $step;

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);

	}

	public function init(){
		global $dao;
		$this->restricted = true;
		$this->message = "";
		$this->title = "Выбор типа публикаций для добавления";
		$this->template = 'select_publ_menu.tpl';

	}
}

class PublicationNavigate extends AbstractNavigate{

	private $do;
	private $id;
	private $type_publ;
	private $request;

	public function __construct($smarty, $session, $doid, $request=null){
		parent::__construct($smarty, $session);

		$this->do = $doid['do'];
		$this->id = $doid['id'];
		$this->type_publ = $doid['type_publ'];
		$this->request = $request;
	}

	public function init(){
		global $dao;

		$publ_types = $dao->getDicValues("type");
		$entity = new Publication();
		$this->smartyArray['type_publ']= $this->type_publ;
		$this->smartyArray['entity']= "publication";
		$this->smartyArray['generate_script']= $this->generateJScript($publ_types);




		switch($this->type_publ){
			case "paper_classik":
				$this->title = "Статья";
				$this->smartyArray['types']= $publ_types;
				$publ_journals = $dao->getDicValues("journal", "name");
				$this->smartyArray['journals']= $publ_journals;
				$this->template = 'publication.tpl';
				$entity->type_id = 1;
				break;
					
			case "tezis_paper_spec":
				$this->title = "Статья в спец. выпуске журнала конференции(семинара, симпозиума), сборников трудов";
				$this->smartyArray['types']= $publ_types;
				$publ_journals = $dao->getDicValues("journal", "name");
				$this->smartyArray['journals']= $publ_journals;
				$this->smartyArray['conferences']= $dao->getDicValues("conference");
				$this->smartyArray['conference_types']= $dao->getDicValues("conference_type");
				$this->smartyArray['conference_levels']= $dao->getDicValues("conference_level");

				//$this->smartyArray['conference_type_pubs']= $dao->getDicValues("conference_type_pub");
				$this->template = 'tezis_paper_spec.tpl';
				$entity->type_id = 3;
				$entity->tezis_type = 'paper_spec';
				break;

			case "tezis_paper":
				$this->title = "Статья в материалах ( работа более 3 страниц) конференции(семинара, симпозиума), сборников трудов";
				$this->smartyArray['types']= $publ_types;
				$this->smartyArray['conferences']= $dao->getDicValues("conference");
				$this->smartyArray['conference_types']= $dao->getDicValues("conference_type");
				$this->smartyArray['conference_levels']= $dao->getDicValues("conference_level");
				$this->template = 'tezis_paper.tpl';
				$entity->type_id = 3;
				$entity->tezis_type = 'paper';
				break;

			case "tezis_tezis":
				$this->title = "Тезис из материалов конференции(семинара, симпозиума), сборников трудов";
				$this->smartyArray['conferences']= $dao->getDicValues("conference");
				$this->smartyArray['conference_types']= $dao->getDicValues("conference_type");
				$this->smartyArray['conference_levels']= $dao->getDicValues("conference_level");

				//$this->smartyArray['conference_type_pubs']= $dao->getDicValues("conference_type_pub");
				$this->template = 'tezis.tpl';
				$entity->type_id = 3;
				$entity->tezis_type = 'tezis';
				break;

			case "patent":
				$this->title = "Авторское изобретение (патент, предпатент и т.д.)";
				$this->smartyArray['patent_types']= $dao->getDicValues("patent_type");
				$this->template = 'patent.tpl';
				$entity->type_id = 5;
				break;

			case "book":
				$this->title = "Книга (монография)";
				$this->template = 'book.tpl';
				$entity->type_id = 2;
				break;

			case "method_recom":
				$this->title = " Методические рекомендации";
				$this->template = 'method_recom.tpl';
				$entity->type_id = 6;
				break;


		}

		$this->smartyArray['readonly']="readonly='readonly'";
		$this->smartyArray['disabled']="disabled='disabled'";
		$this->smartyArray['edit']=false;
		$this->smartyArray['class_req_input']="class='read_only_input'";
		$this->smartyArray['class']="class='read_only_input'";

		if(isset($this->request['ce']) ){
			$this->smartyArray['can_edit']=$this->request['ce'];
		}

		if($_SESSION["user"]['role'] == 'secretary'){
			$this->smartyArray['can_edit'] = 1;
		}

		switch($this->do){
			case "view":
				$entity->id= $this->id;
				$entity = $this->getPub($entity);
				break;

			case "save":

				$entity = $dao->parse_form_to_object($this->request);
				if($entity->id > 0){
					$inserId = $dao->update($entity);
					$this->smartyArray['message'] = "Данные обновлены";
				}else{
					$inserId = $dao->insert($entity);
					$entity->id = $inserId;
					$this->smartyArray['message'] = "Данные сохранены";
				}
				$entity = $this->getPub($entity);



				//				$entity->conference_date_start=getFormatStringFromDate($entity->conference_date_start);
				//				$entity->conference_date_finish=getFormatStringFromDate($entity->conference_date_finish);
				$entity->patent_date=getFormatStringFromDate($entity->patent_date);
				//echo "<h1>" . $entity->patent_date . "</h1>";


				break;

			case "edit":
				if($this->id == 0){
					//	$type_id222 =$entity->type_id;
					//$entity = TestObjectCreator::createTstObject(new Publication());
					//	$entity->language= "russian";
					//	$entity->type_id= $type_id222;
					//$entity->id=null;
					$entity->electron = isset($this->request['electron']) ? 1: 0;

				}else{
					$entity->id= $this->id;
					$entity = $this->getPub($entity);



					//$entity->conference_date_start=getFormatStringFromDate($entity->conference_date_start);
					//$entity->conference_date_finish=getFormatStringFromDate($entity->conference_date_finish);
					$entity->patent_date=getFormatStringFromDate($entity->patent_date);
					//echo "<h1>" . $entity->patent_date . "</h1>";



				}
				$this->smartyArray['readonly']="";
				$this->smartyArray['disabled']="";
				$this->smartyArray['class']="";
				$this->smartyArray['class_req_input']="class='req_input'";
				$this->smartyArray['edit']=true;
				break;
		}

		//$this->converDatesToFormVariant($entity);
		$this->smartyArray['object']= $entity;
	}

	private function getPub($entity){
		global $dao;
		$entity = $dao->get($entity);
		$pubUser = new PublicationUser();
			
		$queryFoPubUsetr = sprintf("select * from bibl_publication_user where publication_id='%s' and user_id='%s'", $entity->id, $_SESSION['user']['id']);
		$pubUserRows = $dao->selectJustNative($queryFoPubUsetr);
			
		if($pubUserRows){
			//$pubUser->id = $pubUserRows[0]['id'];
			//$pubUser->responsible = $pubUserRows[0]['responsible'];
			$pubUser->coauthor = $pubUserRows[0]['coauthor'];
		}else{
			//$pubUser->id = $pubUserRows[0][0];
			///$pubUser->responsible = 0;
			$pubUser->coauthor = 0;
		}
		//$entity->responsible= $pubUser->responsible;
		$entity->coauthor= $pubUser->coauthor;
		return $entity;
	}

	private function converDatesToFormVariant(&$entity){
		$entity->conference_date_start=getFormatStringFromDate($entity->conference_date_start);
		$entity->conference_date_finish=getFormatStringFromDate($entity->conference_date_finish);
		$entity->patent_date=getFormatStringFromDate($entity->patent_date);

	}


	private function  generateJScript($publ_types){
		$script= "<script>%s\n</script>";
		$function1 = "\nfunction getArraReferences(){\nreturn [";
		foreach ($publ_types as $dic) {
			$function1 .=  "[". $dic->id . ", '" .  $dic->value . "'],\n";
		}
		$function1 .= "];\n}\n";
		$function2 = "\nfunction getCurrentUser(){\n";
		$user = $_SESSION['user'];
		$function2 .= "var user = new Object();\n";
		$function2 .= "user.id = " . $user['id'] . ";\n";
		$function2 .= "user.l_name = '" . $user['last_name'] . "';\n";
		$function2 .= "user.f_name = '" . $user['first_name'] . "';\n";
		$function2 .= "user.p_name = '" . $user['patronymic_name'] . "';\n";
		$function2 .= "user.org = '" . ORG_NAME . "';\n";
		$function2 .= "return user;\n}\n";
		return sprintf($script, $function1 . $function2);

	}

}

class ContactNavigate extends AbstractNavigate{

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		$this->title = "Контакты";
		$this->template = 'contacts.tpl';
		$this->smartyArray['admin_email']= ADMIN_EMAIL;
	}
}

class ListNavigate extends AbstractNavigate{
	public  $type_publ;
	public  $user_id;
	public  $responsible_coauthor;
	public  $all_rows_need = true;
	public  $nums_all;
	public  $nums_selected;
	public  $download=false;


	public function __construct($smarty, $session, $request){
		parent::__construct($smarty, $session);
		if(isset($request["type_publ"])){
			$this->type_publ = $request["type_publ"];
		}else{
			$this->type_publ = 'all';
		}

		$this->user_id =  $_SESSION['user']['id'];
		/*if(isset($request["users"]) && $request["users"] == "all"){
			$this->user_id = null;
			$this->smartyArray['users'] = "all";
			}else{
			$this->user_id =  $_SESSION['user']['id'];
			$this->smartyArray['users'] = "mine";
			}*/

		$this->smartyArray['registrator_author']=1;
		$this->smartyArray['registrator_notauthor']=1;
		$this->smartyArray['notregistrator_author']=1;
		$this->smartyArray['notregistrator_notauthor']=1;

		if(isset($request['download'])){
			$this->download = true;
		}

		if(isset($request['filter'])){
			$this->smartyArray['registrator_author']=$request['registrator_author'];
			$this->smartyArray['registrator_notauthor']=$request['registrator_notauthor'];
			$this->smartyArray['notregistrator_author']=$request['notregistrator_author'];
			$this->smartyArray['notregistrator_notauthor']=$request['notregistrator_notauthor'];

			if($request['registrator_author']==1){
				$this->responsible_coauthor[] = array('responsible'=>1,'coauthor'=>1);
			}else{
				$this->all_rows_need = false;
			}

			if($request['registrator_notauthor']==1){
				$this->responsible_coauthor[] = array('responsible'=>1,'coauthor'=>0);
			}else{
				$this->all_rows_need = false;
			}

			if($request['notregistrator_author']==1){
				$this->responsible_coauthor[] = array('responsible'=>0,'coauthor'=>1);
			}else{
				$this->all_rows_need = false;
			}

			if($request['notregistrator_notauthor']==1){
				$this->responsible_coauthor[] = array('responsible'=>0,'coauthor'=>0);
			}else{
				$this->all_rows_need = false;
			}
		}
	}

	public function init(){
		global $dao;
		$this->restricted = true;

		if($this->user_id == null){
			$this->title = "Список публикаций всех сотрудников";
		}else{
			$this->title = "Список моих публикаций";
		}


		$this->template = 'list.tpl';

		$this->message = "";

		$pub_array = array();
		$publs = array();

		$this->smartyArray['type_publ'] = $this->type_publ;

		if($this->all_rows_need){
			$publs = $dao->getFilteredPublicationsRespCoauthor($this->user_id);
		}else{
			$publs = $dao->getFilteredPublicationsRespCoauthor($this->user_id, $this->responsible_coauthor);
		}

		switch ($this->type_publ){
			case 'all':
					
				$publs = $dao->getFilteredPublicationsByType($publs);
				break;

			case "paper":
				$publs = $dao->getFilteredPublicationsByType($publs, PAPER);
				break;

			case "book":
				$publs = $dao->getFilteredPublicationsByType($publs, BOOK);
				break;

			case "tezis":
				$publs = $dao->getFilteredPublicationsByType($publs, TEZIS);
				break;

			case "elres":
				$publs = $dao->getFilteredPublicationsByType($publs, ELRES);
				break;

			case "patent":
				$publs = $dao->getFilteredPublicationsByType($publs, PATENT);
				break;

			case "method_recom":
				$publs = $dao->getFilteredPublicationsByType($publs, METHOD_RECOM);
				break;

		}
			

		$object = new Publication();
       $pub_array = getPublicationsAsArrayPresentation($publs);
	
		if($this->authorized){
			$this->nums_all = count($dao->getAll(new Publication()));
			$this->nums_selected = count($publs);

			$this->smartyArray['publications'] = $pub_array;
			$this->smartyArray['nums_all'] = $this->nums_all;
			$this->smartyArray['nums_selected'] =$this->nums_selected;





			if($this->download){
				//$this->template = 'list_download.tpl';
				header ("Content-type: text/plain");
				$file_name = "list_publications_"  . date("m.d.Y") . ".txt";

				if (ob_get_level()) {
					ob_end_clean();
				}
				// заставляем браузер показать окно сохранения файла
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=' . $file_name);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				//header('Content-Length: ' . filesize($file));
				// читаем файл и отправляем его пользователю
				//readfile($file);
				//exit;

				$i = 0;
				foreach ($pub_array as $item){
					echo (++$i . ". ");

					$auth_index = 0;
					foreach ($item['authors_array'] as $a){
						if($auth_index++ > 0){
							echo (", ");
						}
						echo ($a->last_name . " " . $a->first_name . "." . $a->patronymic_name . ".");
					}
					echo(" ". $item['name'] ." // " .$item['source'] . ". " . $item['year'] . "\n");
				}
				exit;
			}
		}

	}

	
}

class ListPossibleNavigate extends AbstractNavigate{


	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		global $dao;
		$this->restricted = true;
		$this->title = "Список возможных ваших публикаций";
		$this->template = 'list_possible.tpl';
		$this->message = "";
		

		//		$object1 = TestObjectCreator::createTstObject(new Publication());
		//		$object2 = TestObjectCreator::createTstObject(new Publication());
		//		$object3 = TestObjectCreator::createTstObject(new Publication());
		//

		//var_dump($publs);
		if($this->authorized){
			$publsPossibleIsAuthor = $dao->getPossiblePublications();
			//$this->smartyArray['publications'] = array($object1,$object2,$object3);
			$this->smartyArray['publications'] = getPublicationsAsArrayPresentation($publsPossibleIsAuthor);
		}
	}
}


class IndexNavigate extends AbstractNavigate{
	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		global $dao;
		$this->restricted = true;
		$this->title = "Главная страница";
		$this->template = 'index.tpl';
		$this->smartyArray['statistica'] = "";

		if($this->authorized){
			$publsPossibleIsAuthor = $dao->getPossiblePublications();
			$lengthArray = count($publsPossibleIsAuthor);
			if($lengthArray > 0){
				$message = "Найдено " . $lengthArray . " работы, соавтором которых Вы возможно являетесь. Перейдите по данной ссылке, для потдверждения.";
				$message .= "<a href=index.php?page=list_possible> Перейти</a>";
				$this->smartyArray['message'] ='<div style="color: green; width: 700px; padding: 50px; ">' . $message . "</div>";
			}


			//$stat_query = "select t.name, p.type_id, count(*) as quantity from bibl_publication p INNER JOIN bibl_type t ON p.type_id=t.id GROUP BY p.type_id";
			
			$stat_query = "select t.name, p.tezis_type, p.type_id, count(*) as quantity from bibl_publication p " . 
                           " INNER JOIN bibl_type t ON p.type_id=t.id " . 
                           " GROUP BY p.type_id,p.tezis_type";


			$rows = $dao->selectJustNative($stat_query);
			$statistica = array();

			foreach ($rows as $key => $value) {
				$item = array();
				$name = "";
				$item['quantity'] = $value['quantity'];
				$item['type_id'] =$value['type_id'];
				if($value['type_id'] == PAPER){
					$name = "Статей";
				}elseif($value['type_id'] == BOOK){
					$name = "Книг";
				}elseif($value['type_id'] == TEZIS){
					
					if($value['tezis_type'] == 'paper_spec'){
					     $name = "Конференции - статьи в спец. выпуске";
					}elseif($value['tezis_type'] == 'paper'){
						 $name = "Конференции - статьи";
					}elseif($value['tezis_type'] == 'tezis'){
						$name = "Конференции- тезисы";
					}
					
				}elseif($value['type_id'] == ELRES){
					$name = "Эл публикаций";
				}elseif($value['type_id'] == PATENT){
					$name = "Охранных документов";
				}elseif($value['type_id'] == METHOD_RECOM){
					$name = "Методичек";
				}
				$item['type_name'] =$name;
				$statistica[]=$item;
			}

			$this->smartyArray['statistica'] = $statistica;




		}
		//var_dump($publsPossibleIsAuthor);


	}

}



class LoginNavigate extends AbstractNavigate{
	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		$this->title = "Вход";
		$this->template = 'login.tpl';
		$this->smartyArray['object'] = new User();
	}
}

class LogoutNavigate extends AbstractNavigate{
	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		$this->title = "Выход";
		$this->template = 'general_message.tpl';
		$this->message = "До встречи!";
		$this->smartyArray['authorized'] = false;
		$this->smartyArray['result'] = true;
		$_SESSION = array(); //Очищаем сессию
		//session_destroy(); //Уничтожаем
	}

}

class FeedbackNavigate extends AbstractNavigate{

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		$this->title = "Обратная связь";
		$this->template = 'feedback.tpl';
		$email = "";
		if($this->authorized){
			$email = $this->session['user']['username_email'];
		}

		$this->smartyArray['email']= $email;
			
	}
}

class RegistrationNavigate extends AbstractNavigate{

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		$this->title = "Регистрация";
		$this->template = 'register.tpl';
		$this->smartyArray['result']= 0;
		$this->smartyArray['object']= new User();

	}
}

class SavePossibleNavigate extends AbstractNavigate{

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		global  $dao;
		//echo "init work in ActivationNavigate<p>";

		$this->title = "Сохранение публикаций";
		$this->template = 'index.tpl';
		//$this->smartyArray['result']= 0;

		$ids = $_REQUEST['ids'];

		//var_dump($ids);

		foreach ($ids as $pub_id) {
			$entity = new PublicationUser();
			$entity->user_id = $_SESSION['user']['id'];
			$entity->publication_id = $pub_id;
			$entity->responsible = 0;
			$entity->coauthor = 1;

			$dao->insert($entity);
		}




		$this->smartyArray['result']= true;
		$this->message = "Публикации добавлены!";

	}
}

class ActivationNavigate extends AbstractNavigate{

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		global  $dao;
		//echo "init work in ActivationNavigate<p>";

		$this->title = "Активация учетной записи";
		$this->template = 'general_message.tpl';
		$this->smartyArray['result']= 0;

		$result_activation = $dao->activate_user($_REQUEST['username_email']);
		if($result_activation){
			$this->smartyArray['result']= true;
			$this->message = "Уважаемый " . $_REQUEST['username_email'] . ", ваша учетная запись активирована!";
		}else{
			$this->smartyArray['result']= false;
			$this->message = "Уважаемый " . $_REQUEST['username_email'] . ", ваша учетная запись не активирована, обратитесь а администратору";
		}
	}
}


class EditNavigate extends AbstractNavigate{

	public function __construct($smarty, $session){
		parent::__construct($smarty, $session);
	}

	public function init(){
		global  $dao;




		//  $edntityEdit = EntityEditFabrica::createEntityEdit($entity);

		//		$this->title = "Активация учетной записи";
		//		$this->template = 'general_message.tpl';
		//		$this->smartyArray['result']= 0;


	}


}










?>



<?php
session_start();



//exit("exit on index.php");

include_once 'includes/global.php';



$page = isset($_REQUEST['page'])== true ? $_REQUEST['page'] : null ;
$doid= array("do" => "view", "id" => 0, "type_publ" => "" );

if(isset($_REQUEST['do'])){
	$doid['do'] = $_REQUEST['do'];
}

if(isset($_REQUEST['id'])){
	$doid['id'] = $_REQUEST['id'];
}

if(isset($_REQUEST['type_publ'])){
	$doid['type_publ'] = $_REQUEST['type_publ'];
}



$nav_obj = FabricaNavigate::createNavigate($page, $_SESSION, $_REQUEST, $doid);


$nav_obj->display();
//var_dump($nav_obj);

/*

if(!isset($_SESSION["authorized"]) || $_SESSION["authorized"] != 1){
	$page = "login";
	//header("Location: login.php");
    //exit;
}
//include_once 'includes/check_session.php';
$smarty->assign('message',"");


switch ($page){


	case 'contacts':
        
		$smarty->assign('title',"РљРѕРЅС‚Р°РєС‚С‹");
		$smarty->display('templates/contacts.tpl');
		break;

	case 'feedback':

		$smarty->assign('title',"РћР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ");
		$smarty->display('templates/feedback.tpl');
		
		break;

	case 'register':
		
        $smarty->assign('result',0);
        $smarty->assign('title',"Р РµРіРёСЃС‚СЂР°С†РёСЏ");
		$smarty->display('templates/register.tpl');
		
		break;

	case 'activate_account':

		if($dao->activate_user($_REQUEST['username_email'])){
			$smarty->assign('result',true);
			$smarty->assign('message',"РЈРІР°Р¶Р°РµРјС‹Р№ " . $_REQUEST['username_email'] . ", РІР°С€Р° СѓС‡РµС‚РЅР°СЏ Р·Р°РїРёСЃСЊ Р°РєС‚РёРІРёСЂРѕРІР°РЅР°!");
		}else{
			$smarty->assign('result',false);
			$smarty->assign('message',"РЈРІР°Р¶Р°РµРјС‹Р№ " . $_REQUEST['username_email'] . ", РІР°С€Р° СѓС‡РµС‚РЅР°СЏ Р·Р°РїРёСЃСЊ РЅРµ Р°РєС‚РёРІРёСЂРѕРІР°РЅР°, РѕР±СЂР°С‚РёС‚РµСЃСЊ Р° Р°РґРјРёРЅРёСЃС‚СЂР°С‚РѕСЂСѓ");
		}
		$smarty->assign('title',"РђРєС‚РёРІР°С†РёСЏ СѓС‡РµС‚РЅРѕР№ Р·Р°РїРёСЃРё");

		$smarty->display('templates/general_message.tpl');
		break;

	case 'login':
		
		$smarty->assign('title',"Р’С…РѕРґ");
		$smarty->display('templates/login.tpl');
		break;

	case 'logout':

	 $_SESSION = array(); //РћС‡РёС‰Р°РµРј СЃРµСЃСЃРёСЋ
	 session_destroy(); //РЈРЅРёС‡С‚РѕР¶Р°РµРј
	 //    session_unregister('authorized');
	 //    session_unregister('logged_user');
	 $smarty->assign('message',"Р”Рѕ РІСЃС‚СЂРµС‡Рё!");
	 $smarty->assign('title',"Р’С‹С…РѕРґ");
	 $smarty->assign('authorized',false);
	 $smarty->assign('result',true);
	 $smarty->display('templates/general_message.tpl');
	 
	 break;

	case 'list_abs_data':

		$id_trans='РљРѕРґ';
		$last_name_trans='Р¤Р°РјРёР»РёСЏ';
		$first_name_trans='Р�РјСЏ';
		$patronymic_name_trans='РћС‚С‡РµСЃС‚РІРѕ';
		$sex_id_trans='РџРѕР»';
		$sex_trans='';
		$date_birth_string_trans='Р”Р°С‚Р° СЂРѕР¶РґРµРЅРёСЏ';
		$year_birth_trans='Р“РѕРґ СЂРѕР¶РґРµРЅРёСЏ';
		$weight_kg_trans='Р’РµСЃ (РєРі)';
		$height_sm_trans='Р РѕСЃС‚ (СЃРј)';
		$prof_or_other_hazards_yes_no_id_trans='РџСЂРѕС„ РёР»Рё РёРЅС‹Рµ РІСЂРµРґРЅРѕСЃС‚Рё (РґР°, РЅРµС‚)';
		$prof_or_other_hazards_yes_no_trans='';
		$prof_or_other_hazards_discr_trans='РџСЂРѕС„ РёР»Рё РёРЅС‹Рµ РІСЂРµРґРЅРѕСЃС‚Рё (РѕРїРёСЃР°РЅРёРµ)';
		$nationality_id_trans='РќР°С†РёРѕРЅР°Р»СЊРЅРѕСЃС‚СЊ';
		$nationality_trans='';
		$smoke_yes_no_id_trans='';
		$smoke_yes_no_trans='';
		$smoke_discr_trans='';
		$hospital_trans='';
		$doctor_trans='';
		$comments_trans='';
		$user_trans='';
		$insert_date_trans='';

        $smarty->assign('title',"РћС‚СЃСѓС‚СЃРІСѓСЋС‰РёРµ РґР°РЅРЅС‹Рµ");
		$smarty->assign('patients',$dao->getPatients());
		$smarty->display('templates/list_abs_data.tpl');
		
     break;
	case "list":
		$smarty->assign('title',"РЎРїРёСЃРѕРє РїР°С†РёРµРЅС‚РѕРІ");
		$smarty->assign('patients',$dao->getPatients());
		$smarty->display('templates/list.tpl');
		
		break;
	default:
		//echo "nothing";
		//$smarty->assign('patients',$dao->getPatients());
		$smarty->assign('title',"Р“Р»Р°РІРЅР°СЏ СЃС‚СЂР°РЅРёС†Р°");
		$smarty->display('templates/index.tpl');
		
		break;
}

*/
?>
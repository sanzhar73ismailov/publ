<?php
class Publication{

	public $id;
	public $name;

	public $electron;
	public $url;
	public $doi;

	public $abstract_original;
	public $abstract_rus;
	public $abstract_kaz;
	public $abstract_eng;
	public $language;
	public $keywords;
	public $number_ilustrations;
	public $number_tables;
	public $number_references;
	public $number_references_kaz;
	public $code_udk;
	public $type_id;

	public $journal_id;

	public $year;
	public $month;
	public $day;
	public $number;
	public $volume;
	public $issue;
	public $p_first;
	public $p_last;
	public $pmid;

	public $conference_id;
	public $tezis_type;

	public $user_id;

	public $responsible;

	public $patent_type_id;
	public $patent_type_number;
	public $patent_date;

	public $book_city;
	public $book_pages;
	public $izdatelstvo;

	public $method_recom_bbk;
	public $isbn;
	public $method_recom_edited;
	public $method_recom_stated;
	public $method_recom_approved;
	public $method_recom_published_with_the_support;
	public $method_recom_reviewers;

	public $user;
	public $insert_date;
	
	/* foreign keys (arrays) */
	public $authors_array = array();
	public $references_array = array();

   public function getFields() {
         return get_object_vars($this);
    }
}



class Author{
	public $id;
	public $publication_id;
	public $last_name;
	public $first_name;
	public $patronymic_name;
	public $organization_name;
	public $organization_id;
	public $user;
	public $insert_date;

	public function __toString(){
		return $this->id . ", publication_id" . $this->publication_id . ", last_name" . $this->last_name;
	}

}

class Reference{
	public $id;
	public $publication_id;
	public $type_id;
	public $name;
	public $user;
	public $insert_date;

	public function __toString(){
		return $this->id . ", publication_id" . $this->publication_id . ", name" . $this->name;
	}
}

class PublicationUser{
	public $id;
	public $publication_id;
	public $user_id;
	public $responsible;
	public $coauthor;
}

class Journal{
	public $id;
	public $name;
	public $country;
	public $issn;
	public $periodicity;
	public $izdatelstvo_mesto_izdaniya;
	public $user;
	public $insert_date;
}

class Conference{
	public $id;
	public $name;
	public $sbornik_name;
	public $city;
	public $country;
	public $type_id;
	public $level_id;
	public $date_start;
	public $date_finish;
	public $add_info;
	public $user;
	public $insert_date;
}
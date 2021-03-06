<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Semesters_subjects extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(!$this->session->userdata("isLoggedIn")){
			redirect(site_url("homepage"));	
		}
	}
	function index()
	{
		$data["title"] = "Semesters & Subjects";
		$data["user"] = $this->session->userdata("isLoggedIn");
		$this->load->view("semesters_subjects/index", $data);
	}
	function add_semester(){
		$user = $this->session->userdata("isLoggedIn");
		$semester = $this->input->post("semester");
		$semester = "Semester ".$semester;
		$query = $this->common_model->query("SELECT * FROM semesters WHERE semesters.semester = '".$semester."' AND semesters.user_id='".$user->id."'");
		if ($query->num_rows()>0){
			echo 'Sorry! Semester is already exists.';
		}else{
			$data["user_id"] = $user->id;
			$data["semester"] = $semester;
			$this->common_model->insert("semesters", $data);
			echo '1';
		}
	}
	function add_subject(){
		$user = $this->session->userdata("isLoggedIn");
		$semester_id = $this->input->post("semester_id");
		$subjects = $this->input->post("subjects");
		$query = $this->common_model->query("SELECT * FROM subjects WHERE subjects.subjects = '".$subjects."' AND subjects.user_id = ".$user->id."");
		$department = $this->common_model->query("SELECT * FROM departments WHERE id = ".$user->department."")->row();
		if ($query->num_rows()>0){
			echo 'Sorry! Subject is already exists.';
		}else{
			$data["user_id"] = $user->id;
			$data["department"] = $department->name;
			$data["semester_id"] = $semester_id;
			$data["subjects"] = $subjects;
			$this->common_model->insert("subjects", $data);
			echo '1';
		}
	}
	function delete_semester($id){
		/*$user = $this->session->userdata("isLoggedIn");
		$sem = $this->common_model->query("SELECT semester FROM semesters WHERE semesters.id = ".$id."")->row();
		$sem = slug($sem->semester);
		$user->user_type;
		$dir = "./uploads/".strtolower($user->type_name).'/'.$user->user_name.'/'.$sem;
		array_walk(glob($dir . '/*'), function ($fn) {
			if (is_file($fn))
				chown($fn,0777);
				unlink($fn);
		});
		chown($dir,0777);
		unlink($dir);
exit;		
		$documents = $this->common_model->query("SELECT documents.subject_id FROM documents WHERE documents.semester_id = ".$id." GROUP BY documents.subject_id");
		if($document->num_rows()>0){
			foreach($document->result() as $doc){
				$documents = $this->common_model->query("SELECT documents.subject_id FROM documents WHERE documents.subject_id = ".$doc->subject_id."");
			}
		}
		
		
		$document = $this->common_model->query("SELECT * FROM documents WHERE documents.semester_id = ".$id."");
		if($document->num_rows()>0){
			foreach($document->result() as $row){
				$path = './uploads/'.$row->doc_paths.'/'.$row->uploaded_filename;
				if(file_exists($path)){
					unlink($path);
				}
				$this->common_model->delete("id",$id,"documents");
			}
		}
		$document = $document->row();
		$dir = './uploads/'.$document->doc_paths.'/';
		if(is_dir($dir)){
			rmdir($dir);
		}
		exit;
		
		$this->common_model->delete("id",$id,"subjects");
		$this->session->set_flashdata("error","Record Deleted...");
		redirect(site_url("semesters_subjects"));
		*/
		
		if(check_delete("subjects", "semester_id", $id)==TRUE){
			$this->session->set_flashdata("error","This semester have multiple subjects so you cannot delete this semester.");
			redirect(site_url("semesters_subjects"));
		}else{
			$this->common_model->delete("id",$id,"semesters");
			$this->session->set_flashdata("error","Record Deleted...");
			redirect(site_url("semesters_subjects"));
		}
	}
	function recursiveRemoveDirectory($directory)
	{
		foreach(glob("{$directory}/*") as $file)
		{
			if(is_dir($file)) { 
				recursiveRemoveDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directory);
	}	
	function delete_subject($id){
		$subjects = $this->common_model->query("SELECT * FROM subjects WHERE subjects.id = ".$id."")->row();
		$document = $this->common_model->query("SELECT * FROM documents WHERE documents.subject_id = ".$id."");
		if($document->num_rows()>0){
			$document = $document->row();	
			$path = './uploads/'.$document->doc_paths.'/';
			if(file_exists($path)){
				$this->recursiveRemoveDirectory($path);
			}
		}
		$this->common_model->delete("subject_id",$id,"documents");
		$this->common_model->delete("id",$id,"subjects");
		$this->session->set_flashdata("error","Record Deleted...");
		redirect(site_url("semesters_subjects"));
	}
}
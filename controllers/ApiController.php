<?php
/**
 * API・JSONで返す
 */
class ApiController extends Controller
{
public function moreAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }
        // ユーザ情報取得
        $user = $this->session->get('user');
        

        $p = $this->request->getPost('page');

        $status_more = $this->db_manager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id'], $p);
        
        return $this->response->json_api($status_more);
    }

    public function user_postsAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }
        // ユーザ情報取得
        $user = $this->session->get('user');

        // ユーザ情報取得
        $user_id = $this->request->getPost('user_id');

        $p = $this->request->getPost('page');

        $status_more = $this->db_manager->get('Status')->fetchAllByUserId($user_id,$user['id'], $p);
        
        return $this->response->json_api($status_more);
    }

    public function get_following_userAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }
        // ユーザ情報取得
        $user = $this->request->getPost('user_id');

        $p = $this->request->getPost('page');

        $status_more = $this->db_manager->get('User')->fetchAllFollowingUserByUserId($user,$p);
        
        return $this->response->json_api($status_more);
    }

    public function get_followers_userAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }
        // ユーザ情報取得
        $user = $this->request->getPost('user_id');

        $p = $this->request->getPost('page');

        $status_more = $this->db_manager->get('User')->fetchAllFollowersUserByUserId($user,$p);
        
        return $this->response->json_api($status_more);
    }
    public function get_all_userAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $p = $this->request->getPost('page');

        $status_more = $this->db_manager->get('User')->all($p);
        
        return $this->response->json_api($status_more);
    }
    public function likesAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $user = $this->session->get('user');

        $status_id = $this->request->getPost('status_id');

        $type = $this->request->getPost('likeType');

        if($type == "up"){
            $this->db_manager->get('Status')->like_insert($user['id'], $status_id);
        }else if($type == "del"){
            $this->db_manager->get('Status')->like_outsert($user['id'], $status_id);
        }

        return $this->response->json_api($status_id);
    }
}
<?php

namespace aircode\autocomplete\controller;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\HttpFoundation\Response;


class autocomplete {
    
    /* @var config */
    protected $config;

    /* @var helper */
    protected $helper;

    /* @var template */
    protected $template;

    /* @var user */
    protected $user;
    
    /** @var \phpbb\db\driver\driver */
	protected $db;
    
    public function __construct(config $config, helper $helper, template $template, user $user, \phpbb\db\driver\driver_interface $db)
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
        $this->db = $db;
    }
    
    public function handle($name) {
        
        $response = array();
        
        /*$response[] = array(
            'forum_name' => 'Test Forum',
            'forum_id' => 2
        );*/
        
        if(mb_strlen($name) < 3) {
            die();
        }
        
        $sql = "SELECT DISTINCT pf.forum_name as search_forum_name, f.forum_id as real_forum_id 
        FROM ".FORUMS_TABLE. " f 
        LEFT JOIN ".FORUMS_TABLE." pf ON (f.parent_id = pf.forum_id) 
        WHERE LOWER(f.forum_name) LIKE LOWER('%".$this->db->sql_escape($name)."%')";
        
        $result = $this->db->sql_query($sql);
        
        while ($data = $this->db->sql_fetchrow($result)) {
            if(isset($data['search_forum_name'])) {
                $response[] = array(
                    'forum_name' => $data['search_forum_name'],
                    'forum_id' => $data['real_forum_id']
                );
            }
        }
        
        return new Response(json_encode($response), 200);
    }
}

?>
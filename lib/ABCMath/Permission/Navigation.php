<?php
namespace ABCMath\Permission;

use ABCMath\Template\Template;
use ABCMath\Base;
use DateTime;

class Navigation extends Base
{
    public $user;

    public $current_section;
    public $current_uri;
    public $semester_id;

    protected $_sections;
    protected $_subsections;
    protected $_template;

    public function __construct()
    {
        parent::__construct();

        $this->user = null;
        $this->current_section = '';
        $this->current_uri = null;

        $this->_sections = array();
        $this->_subsections = array();

        $this->_template = new Template(Template::FILESYSTEM);
    }

    public function setCI_URI(\CI_URI $uri)
    {
        $this->current_uri = $uri;
    }

    public function build(\User_Model $user_model)
    {
        $this->user = $user_model;
    }

    public function display_quicklinks()
    {
        $html = '';
        //logout
        $html .= $this->_template->render('Navigation/quicklinks.twig', array());
        return $html;
    }

    public function display_sections()
    {
        $html = '';

        //sections
        $this->build_sections();
        $html .= implode('', $this->_sections);

        //logout
        $html .= $this->_template->render('Navigation/logout.twig', array());

        return $html;
    }

    public function display_subsections()
    {
        $this->build_subsections();

        return implode('', $this->_subsections);
    }

    public function build_sections()
    {
        $this->_sections = array();
        $active = $this->build_active_flag();
        $html = '';

        /*
        * Build out section structure.
        */

        if (!$this->check_user()) {
            throw new \InvalidArgumentException('Invalid User type.');
        }

        //display users scaffolding link.
        if ($this->user->check_permission(array('admin'))) {
            $this->_sections[] = $this->_template->render('Navigation/setup.twig', $active);
            $this->_sections[] = $this->_template->render('Navigation/user.twig', $active);
            $this->_sections[] = $this->_template->render('Navigation/admin.twig', $active);
        }

        if ($this->user->check_permission(array('admin', 'scaffolding'))) {
            $this->_sections[] = $this->_template->render('Navigation/scaffolding.twig', $active);
        }

        if ($this->user->check_permission($this->material_permission())) {
            $this->_sections[] = $this->_template->render('Navigation/material.twig', $active);
        }
    }

    public function get_all_semesters()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('id, description', 'start_date', 'end_date')
            ->from('semesters', 's')
            ->orderBy('start_date', 'ASC');
        $all_semesters = $qb->execute()->fetchAll();
        $now = new DateTime();

        $options = array('' => 'No Filter');
        foreach ($all_semesters as $sem) {
            $start = new DateTime($sem['start_date']);
            $end = new DateTime($sem['end_date']);

            if (($start < $now) && ($now < $end)) {
                $this->semester_id = $sem['id'];
            }

            $options[$sem['id']] = $sem['description'];
        }

        return $options;
    }

    /**
     * Builds general sub-sections
     */
    protected function build_subsections()
    {
        $this->_sections = array();

        /*
        * Build out subsection structure.
        */

        switch ($this->current_section) {
            case 'user':
                $this->build_user_subsections();
            break;

            case 'material':
                $this->build_material_subsections();
            break;
        }
    }

    /**
     * Builds the sub-sections for users
     */
    protected function build_user_subsections()
    {
        $subsection = $this->current_uri->segment(2);
        if (in_array($subsection, array('all_user', 'create_user', 'edit_user_group'))) {
            $subsection = 'all_user';
        }

        if (in_array($subsection, array('all_groups', 'create_group', 'edit_group'))) {
            $subsection = 'all_groups';
        }

        $active = array("{$subsection}_active" => 'active');

        $this->_subsections[] = $this->_template->render(
            'Navigation/Subnavigation/user__user.twig',
            $active);
        $this->_subsections[] = $this->_template->render(
            'Navigation/Subnavigation/user__group.twig',
            $active);
    }

    /**
     * Builds the sub-sections for materials
     */
    protected function build_material_subsections()
    {
        $subsection = $this->current_uri->segment(1);
        $active = array("{$subsection}_active" => 'active');

        if ($this->user->check_permission(array('passage.edit', 'passage.view'))) {
            $this->_subsections[] = $this->_template->render(
                'Navigation/Subnavigation/material__passage.twig',
                $active);
        }

        if ($this->user->check_permission(array('vocabulary.edit', 'vocabulary.view'))) {
            $this->_subsections[] = $this->_template->render(
                'Navigation/Subnavigation/material__vocabulary.twig',
                $active);
        }

        if ($this->user->check_permission(
            array('scrambled_paragraph.edit', 'scrambled_paragraph.view'))) {
            $this->_subsections[] = $this->_template->render(
                'Navigation/Subnavigation/material__scrambled_paragraph.twig',
                $active);
        }

        if ($this->user->check_permission(
            array('reading_comprehension.edit', 'reading_comprehension.view'))) {
            $this->_subsections[] = $this->_template->render(
                'Navigation/Subnavigation/material__reading_comprehension.twig',
                $active);
        }
    }

    /**
     * Show which secitons in nav bar should be highlighted.
     * Tells the program what list of sub-navs to show.
     */
    protected function build_active_flag()
    {
        if (in_array($this->current_uri->segment(1),
            array('setup', 'admin', 'scaffolding', 'user', 'material'))) {
            $this->current_section = $this->current_uri->segment(1);
        }

        //material sub sections are still material.
        if (in_array($this->current_uri->segment(1),
            array('passage', 'vocabulary', 'scrambled_paragraph', 'reading_comprehension'))) {
            $this->current_section = 'material';
        }

        return array("{$this->current_section}_active" => 'active');
    }

    protected function material_permission()
    {
        return array(
            'reading_comprehension.edit',
            'reading_comprehension.view',
            'scrambled_paragraph.edit',
            'scrambled_paragraph.view',
            'vocabulary.edit',
            'vocabulary.view',
            'passage.edit',
            'passage.view',
            'scaffolding',
            'admin',
            );
    }

    protected function check_user()
    {
        return $this->user instanceof \User_Model;
    }

    protected function set_user(\User_Model $user_model)
    {
        $this->user = $user_model;
    }
}

<?php
class TopMenuBehavior extends CBehavior
{
        protected function getTopMenu()
        {
                //Yii::app()->cache->flush();
                $topMenu = $this->getCategoriesForMenu();
                if (Yii::app()->user->isGuest) {
                        array_push($topMenu, array(
                                'label'=>'Login',
                                'url'=>array('route'=>'/admin'),
                        ));
                } else {
                        array_push($topMenu, array(
                            'label'=>'Logout ('.Yii::app()->user->name.')',
                            'url'=>array('route'=>'/admin/logout'),
                        ));
                }
                
                return $topMenu;
        }
        
        protected function getCategoriesForMenu()
        {
                return Category::model()->getCategories();
        }
        
        
        
        protected function getCategoriesForSelect()
        {
                return Category::model()->getCategoriesForSelect();
        }
}

<?php

class NewsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
        
        /**
         * This method is called by the application before the controller starts to execute.
         */
        public function init() {
                //Attach the behavior to the controller
                $this->attachBehavior("top_menu",new TopMenuBehavior);
                $this->topMenu = $this->getTopMenu();
        }

        /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 * @throws CHttpException
	 */
	public function actionView($slug=false)
	{
                if ($slug) {
                        $news = $this->loadModelBySlug($slug);
                        $this->render('view',array(
                                'model'=>$news,
                                'comment' => $this->newComment($news),
                        ));
                } else {
                         $this->redirect(array('/'));
                        //throw new CHttpException(404,'Запрашиваемая страница не существует.');
                }
	}
        
        /**
	 * handles the comment form for news.
	 * @param model News
	 */
        public function newComment($news)
        {
                $comment = new Comment();
                
                if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
                {
                        echo CActiveForm::validate($comment);
                        Yii::app()->end();
                }

                if (isset($_POST['Comment'])) {
                        $comment->attributes = $_POST['Comment'];
                        if ($news->addComment($comment)) {
                                if ($comment->status == Comment::STATUS_PENDING) {
                                        Yii::app()->user->setFlash('commentSubmitted','Спасибо за оставленный комментарий.');
                                }
                                $this->refresh();
                        }
                }
                return $comment;
        }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new News;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['News']))
		{
			$model->attributes=$_POST['News'];
                        $model->category = $_POST['News']['category'];
			if($model->save())
                                    $this->redirect(array('/'.$model->slug));
		}

		$this->render('create',array(
			'model'=>$model,
                        'categories'=>$this->getCategoriesForSelect(),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['News']))
		{
			$model->attributes=$_POST['News'];
                        $model->category = $_POST['News']['category'];
                        
			if($model->save())
				$this->redirect(array('/'.$model->slug));
		}

		$this->render('update',array(
			'model'=>$model,
                        'categories'=>$this->getCategoriesForSelect(),
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($category = false)
	{
                $category = (int) $category;
                
                $criteria = new CDbCriteria(array(
                        'condition' => 'status='.News::STATUS_PUBLISHED,
                        'with' => array(
                                'commentCount',
                                'category',
                        ),
                    
                ));
                $criteria->distinct = true;
                
                if ($category) {
                        $criteria->addInCondition('category.id', Category::model()->getChildrenArray($category));
                        $criteria->addCondition('category.id='.$category, 'OR');
                    
                }
                $criteria->addCondition('status='.News::STATUS_PUBLISHED);
                
                $sort = new CSort();
                $sort->sortVar = 'sort';
                $sort->multiSort = true;
                $sort->attributes = array(
                        'create_time' => array(
                                'label' => 'дата создания',
                                'asc' => 'create_time ASC',
                                'desc' => 'create_time DESC',
                                'defaultOrder' => 'DESC'
                        ),
                );
                
                $models = News::model()->with('category')->findAll($criteria);
                
                $dataProvider = new CArrayDataProvider($models, array(
                        'pagination' => array(
                                'pageSize' => 3,
                        ),
                        'sort' => $sort,
                ));
                
                $this->render('index', array(
                        'dataProvider' => $dataProvider,
                ));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new News('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['News']))
			$model->attributes=$_GET['News'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
        
        /**
	 * Error page
	 */
	public function actionError()
	{
		$this->render('error');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return News the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
                if (Yii::app()->user->isGuest) {
                        $condition = 'status='.Post::STATUS_PUBLISHED;
                } else {
                        $condition = '';
                }
                
                $model = News::model()->with('category')->findByPk($id, $condition);
                
		if($model===null)
                       $this->redirect(array('/'));
			//throw new CHttpException(404,'Запрашиваемая страница не существует.');
		return $model;
	}
        
        /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param string $id the slug of the model to be loaded
	 * @return News the loaded model
	 * @throws CHttpException
	 */
        public function loadModelBySlug($slug)
	{
                $criteria=new CDbCriteria;
                
                if (isset($slug)) {
                        $criteria->addSearchCondition('slug', $slug);
                }
                
                if (Yii::app()->user->isGuest) {
                        $criteria->addCondition('status='.News::STATUS_PUBLISHED);
                }
                
                $model = News::model()->find($criteria);
                
		if($model===null)
                        $this->redirect(array('/'));
			//throw new CHttpException(404,'Запрашиваемая страница не существует.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param News $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='news-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

<?php
namespace api\modules\v1\controllers;

use api\modules\v1\controllers\AppController;
use common\models\Post;
use Yii;
use yii\helpers\ArrayHelper;

class PostController extends AppController
{
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authentificator' => [
                'except' => ['index']
            ]
        ]);
    }


    public function verbs()
    {
        return [
            'index' => ['GET', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS']
        ];
    }


    public function actionIndex(): array
    {
        $query = Post::find()->where(['status' => Post::PUBLISHED]);

        $id = $this->request->get('id');
        $userId = $this->request->get('user_id');
        $categoryId = $this->request->get('category_id');
        $firstItem = (int)$this->request->get('first_item', 0);
        $itemCount = (int)$this->request->get('item_count', 10);

        if ($id !== null) {
            $query->andFilterWhere(['id' => $id]);
        }
        if ($userId !== null) {
            $query->andFilterWhere(['user_id' => $userId]);
        }
        if ($categoryId !== null) {
            $query->andFilterWhere(['post_category_id' => $categoryId]);
        }
        if ($id !== null && $userId === null && $categoryId === null) {
            $post = $query->one();

            if ($post) {
                return $this->returnSuccess([
                    'post' => $post,
                ]);
            } else {
                return $this->returnError('Пост не найден.');
            }
        }

        $posts = $query->offset($firstItem)->limit($itemCount)->all();

        return $this->returnSuccess([
            'posts' => $posts,
            'total_count' => $query->count(),
            'first_item' => $firstItem,
            'item_count' => count($posts),
        ]);
    }

    public function actionCreate(): array
    {
        $model = new Post();
        $model->user_id = Yii::$app->user->id;

        $model->post_category_id = $this->request->post('category_id', null);
        $model->title = $this->request->post('title', null);
        $model->text = $this->request->post('text', null);
        $model->status = 0;

        $imageData = $this->request->post('image');
        if ($imageData) {
            list($type, $data) = explode(';', $imageData);
            list(, $data) = explode(',', $data);

            $data = base64_decode($data);

            $randomString = Yii::$app->security->generateRandomString();
            $filePath = Yii::getAlias('@uploads') . '/' . $randomString . '.jpg';

            file_put_contents($filePath, $data);

            $model->image = 'uploads/' . $randomString . '.' . 'jpg';
        }


        if ($model->validate() && $model->save()) {
            return $this->returnSuccess(['post' => $model]);
        }

        return $this->returnError($model->getErrors());
    }

    public function actionUpdate(): array
    {
        $id = $this->request->get('id');
        $model = Post::findOne($id);

        if (!$model) {
            return $this->returnError('Пост не найден.');
        }

        $model->post_category_id = $this->request->post('category_id', $model->post_category_id);
        $model->title = $this->request->post('title', $model->title);
        $model->text = $this->request->post('text', $model->text);
        $model->status = $this->request->post('status', 10);

        if ($model->validate() && $model->save()) {
            return $this->returnSuccess(['post' => $model]);
        }

        return $this->returnError($model->getErrors());
    }


    public function actionDelete(): array
    {
        $id = $this->request->get('id');
        $model = Post::findOne($id);

        if (!$model) {
            return $this->returnError('Пост не найден.');
        }

        if ($model->delete()) {
            return $this->returnSuccess(['message' => 'Пост успешно удален.']);
        }

        return $this->returnError('Ошибка при удалении поста.');
    }
}
<?php 
use yii\web\UploadedFile;
namespace app\models;
use yii\base\Model;
/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'jpg,jpeg,doc,docx,xls,xlsx,pdf'],
        ];
    }
}
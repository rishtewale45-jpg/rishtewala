<?php
require __DIR__.'/../config/config.php';
require_admin();

/*
|--------------------------------------------------------------------------
| Profile Gallery Table
|--------------------------------------------------------------------------
| Main image profiles.photo mein rahegi.
| 4 additional images is separate table mein save hongi.
*/
db()->exec("
    CREATE TABLE IF NOT EXISTS profile_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        profile_id INT NOT NULL,
        image_path VARCHAR(500) NOT NULL,
        sort_order TINYINT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(profile_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

/*
|--------------------------------------------------------------------------
| Delete Profile
|--------------------------------------------------------------------------
*/
if(isset($_POST['delete'])){
    $id=(int)$_POST['delete'];

    db()->prepare('DELETE FROM profile_images WHERE profile_id=?')
       ->execute([$id]);

    db()->prepare('DELETE FROM profiles WHERE id=?')
       ->execute([$id]);
}

/*
|--------------------------------------------------------------------------
| Upload Helper
|--------------------------------------------------------------------------
*/
function upload_profile_image($file, $profileId, $prefix='profile'){
    if(
        empty($file['name']) ||
        $file['error'] !== UPLOAD_ERR_OK
    ){
        return false;
    }

    $ext=strtolower(
        pathinfo($file['name'],PATHINFO_EXTENSION)
    );

    if(!in_array($ext,['png','jpg','jpeg','webp'],true)){
        return false;
    }

    $dir=__DIR__.'/../uploads/';

    if(!is_dir($dir)){
        mkdir($dir,0755,true);
    }

    $name=$prefix.'_'.(int)$profileId.'_'.time().'_'.bin2hex(random_bytes(3)).'.'.$ext;

    if(move_uploaded_file($file['tmp_name'],$dir.$name)){
        return 'uploads/'.$name;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Save Profile
|--------------------------------------------------------------------------
*/
if(
    $_SERVER['REQUEST_METHOD']==='POST' &&
    isset($_POST['save'])
){

    $gender=$_POST['gender']??'female';
    $name=trim($_POST['name']??'');
    $age=(int)($_POST['age']??0);
    $state=trim($_POST['state']??'');
    $city=trim($_POST['city']??'');
    $about=trim($_POST['about']??'');
    $marital_status=trim($_POST['marital_status']??'');
    $annual_income=trim($_POST['annual_income']??'');
    $description=trim($_POST['description']??'');
    $caste=trim($_POST['caste']??'');
    $religion=trim($_POST['religion']??'');
    $whatsapp=trim($_POST['whatsapp']??'');
    $active=isset($_POST['active'])?1:0;

    if($age < 18){
        $error='Profile age must be 18 or above.';
    }elseif($name===''){
        $error='Please enter profile name.';
    }else{

        $id=(int)($_POST['id']??0);

        if($id>0){

            db()->prepare(
                'UPDATE profiles
                 SET gender=?,name=?,age=?,state=?,city=?,about=?,
                     marital_status=?,annual_income=?,description=?,
                     caste=?,religion=?,whatsapp=?,active=?
                 WHERE id=?'
            )->execute([
                $gender,
                $name,
                $age,
                $state,
                $city,
                $about,
                $marital_status,
                $annual_income,
                $description,
                $caste,
                $religion,
                $whatsapp,
                $active,
                $id
            ]);

        }else{

            db()->prepare(
                'INSERT INTO profiles
                (gender,name,age,state,city,about,marital_status,
                 annual_income,description,caste,religion,whatsapp,active)
                 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)'
            )->execute([
                $gender,
                $name,
                $age,
                $state,
                $city,
                $about,
                $marital_status,
                $annual_income,
                $description,
                $caste,
                $religion,
                $whatsapp,
                $active
            ]);

            $id=(int)db()->lastInsertId();
        }

        /*
        |--------------------------------------------------------------------------
        | Main Image
        |--------------------------------------------------------------------------
        */
        if(
            isset($_FILES['main_photo']) &&
            !empty($_FILES['main_photo']['name'])
        ){

            $mainPath=upload_profile_image(
                $_FILES['main_photo'],
                $id,
                'profile_main'
            );

            if($mainPath){
                db()->prepare(
                    'UPDATE profiles SET photo=? WHERE id=?'
                )->execute([
                    $mainPath,
                    $id
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4 Sub Images
        |--------------------------------------------------------------------------
        */
        for($i=1;$i<=4;$i++){

            $field='sub_photo_'.$i;

            if(
                isset($_FILES[$field]) &&
                !empty($_FILES[$field]['name'])
            ){

                $subPath=upload_profile_image(
                    $_FILES[$field],
                    $id,
                    'profile_sub_'.$i
                );

                if($subPath){

                    /*
                    | Replace existing image in this slot
                    */
                    db()->prepare(
                        'DELETE FROM profile_images
                         WHERE profile_id=? AND sort_order=?'
                    )->execute([
                        $id,
                        $i
                    ]);

                    db()->prepare(
                        'INSERT INTO profile_images
                         (profile_id,image_path,sort_order)
                         VALUES(?,?,?)'
                    )->execute([
                        $id,
                        $subPath,
                        $i
                    ]);
                }
            }
        }

        $saved=true;
    }
}

/*
|--------------------------------------------------------------------------
| Change Main Photo From Existing Profile
|--------------------------------------------------------------------------
*/
if(
    $_SERVER['REQUEST_METHOD']==='POST' &&
    isset($_POST['save_photo']) &&
    !empty($_POST['id']) &&
    isset($_FILES['photo'])
){

    $id=(int)$_POST['id'];

    $path=upload_profile_image(
        $_FILES['photo'],
        $id,
        'profile_main'
    );

    if($path){

        db()->prepare(
            'UPDATE profiles SET photo=? WHERE id=?'
        )->execute([
            $path,
            $id
        ]);

        $saved=true;
    }
}

/*
|--------------------------------------------------------------------------
| Existing Profiles
|--------------------------------------------------------------------------
*/
$ps=db()->query(
    'SELECT * FROM profiles ORDER BY gender,id'
)->fetchAll();

?>
<!doctype html>
<html>

<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="../assets/style.css">

<style>
.gallery-upload{
    margin-top:15px;
    padding:15px;
    border:1px solid #eee;
    border-radius:14px;
    background:#fff;
}

.gallery-upload h4{
    margin:0 0 12px;
}

.gallery-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.gallery-box{
    border:1px dashed #ddd;
    padding:10px;
    border-radius:12px;
}

.gallery-box label{
    display:block;
    font-weight:600;
    margin-bottom:6px;
}

.gallery-note{
    color:#777;
    font-size:13px;
    margin:5px 0 12px;
}
</style>

</head>

<body>

<main class="phone">

<header>
<a href="index.php">←</a>
<b>Profiles</b>
</header>

<section class="card">

<h3>Add / Edit Profile</h3>

<?php if(isset($error)):?>
<div style="background:#ffe8e8;color:#b00020;padding:12px;border-radius:10px;margin-bottom:15px;">
<?=h($error)?>
</div>
<?php endif;?>

<?php if(isset($saved)):?>
<div class="success">Profile updated successfully.</div>
<?php endif;?>

<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id" value="">

<select name="gender">
<option value="female">Female</option>
<option value="male">Male</option>
</select>

<input name="name" placeholder="Name" required>

<input
    name="age"
    type="number"
    min="18"
    placeholder="Age (18+)"
    required
>

<input name="state" placeholder="State">
<input name="city" placeholder="City">

<textarea name="about" placeholder="About Me"></textarea>

<input name="marital_status" placeholder="Marital Status">

<input name="annual_income" placeholder="Annual Income">

<textarea name="description" placeholder="Description"></textarea>

<input name="caste" placeholder="Caste">

<input name="religion" placeholder="Religion">

<input name="whatsapp" placeholder="WhatsApp">

<div class="gallery-upload">

<h4>🖼️ Profile Photos</h4>

<p class="gallery-note">
Main image + 4 subimages upload karein. Phone se direct photo select kar sakte hain.
</p>

<div class="gallery-grid">

<div class="gallery-box">
<label>Main Image</label>
<input
    type="file"
    name="main_photo"
    accept="image/png,image/jpeg,image/webp"
>
</div>

<div class="gallery-box">
<label>Sub Image 1</label>
<input
    type="file"
    name="sub_photo_1"
    accept="image/png,image/jpeg,image/webp"
>
</div>

<div class="gallery-box">
<label>Sub Image 2</label>
<input
    type="file"
    name="sub_photo_2"
    accept="image/png,image/jpeg,image/webp"
>
</div>

<div class="gallery-box">
<label>Sub Image 3</label>
<input
    type="file"
    name="sub_photo_3"
    accept="image/png,image/jpeg,image/webp"
>
</div>

<div class="gallery-box">
<label>Sub Image 4</label>
<input
    type="file"
    name="sub_photo_4"
    accept="image/png,image/jpeg,image/webp"
>
</div>

</div>

</div>

<label>
<input type="checkbox" name="active" checked>
Active
</label>

<button name="save" value="1">
Save Profile
</button>

</form>

</section>


<section class="card">

<h3>Existing Profiles</h3>

<?php foreach($ps as $p):?>

<div style="padding:15px 0;border-bottom:1px solid #eee">

<b><?=h($p['name'])?></b>
· <?=h($p['gender'])?>
· <?=h($p['city'])?>

<form
    method="post"
    enctype="multipart/form-data"
    style="margin-top:12px;"
>

<input
    type="hidden"
    name="id"
    value="<?=$p['id']?>"
>

<label>Main Photo Change</label>

<input
    type="file"
    name="photo"
    accept="image/*"
>

<button
    name="save_photo"
    value="1"
>
Change Main Photo
</button>

<button
    name="delete"
    value="<?=$p['id']?>"
    style="background:#444"
>
Delete
</button>

</form>

</div>

<?php endforeach;?>

</section>

</main>

<script src="/rishtewale-protection.js"></script>

</body>
</html>

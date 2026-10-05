<?php
require __DIR__.'/../config/config.php';
require_admin();

$keys = [
 'home_heading','home_subtitle','home_online_count','home_note',
 'home_profile_1_name','home_profile_1_city','home_profile_1_photo',
 'home_profile_2_name','home_profile_2_city','home_profile_2_photo',
 'home_profile_3_name','home_profile_3_city','home_profile_3_photo',
 'home_profile_4_name','home_profile_4_city','home_profile_4_photo',
 'home_profile_5_name','home_profile_5_city','home_profile_5_photo',
 'home_profile_6_name','home_profile_6_city','home_profile_6_photo',
 'female_banner_title','female_banner_subtitle','female_banner_image',
 'female_earning_title','female_earning_subtitle',
 'home_slider_interval'
];
for($i=1;$i<=3;$i++){
  $keys[]="home_banner_{$i}_active";
  $keys[]="home_banner_{$i}_title";
  $keys[]="home_banner_{$i}_subtitle";
  $keys[]="home_banner_{$i}_image";
  $keys[]="home_banner_{$i}_button";
  $keys[]="home_banner_{$i}_link";
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach($keys as $k){
    if(isset($_POST[$k])){
      db()->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)')->execute([$k,trim($_POST[$k])]);
    }
  }
  for($i=1;$i<=3;$i++){
    $field="home_banner_{$i}_upload";
    if(!empty($_FILES[$field]['name']) && $_FILES[$field]['error']===0){
      $ext=strtolower(pathinfo($_FILES[$field]['name'],PATHINFO_EXTENSION));
      if(in_array($ext,['png','jpg','jpeg','webp'],true)){
        $dir=__DIR__.'/../uploads/'; if(!is_dir($dir)) mkdir($dir,0755,true);
        $name='home_banner_'.$i.'_'.time().'.'.$ext;
        if(move_uploaded_file($_FILES[$field]['tmp_name'],$dir.$name)){
          db()->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)')->execute(["home_banner_{$i}_image",'uploads/'.$name]);
        }
      }
    }
  }
  if(!empty($_FILES['female_banner_upload']['name']) && $_FILES['female_banner_upload']['error']===0){
    $ext=strtolower(pathinfo($_FILES['female_banner_upload']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['png','jpg','jpeg','webp'],true)){
      $dir=__DIR__.'/../uploads/'; if(!is_dir($dir)) mkdir($dir,0755,true);
      $name='female_banner_'.time().'.'.$ext;
      if(move_uploaded_file($_FILES['female_banner_upload']['tmp_name'],$dir.$name)){
        db()->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)')->execute(['female_banner_image','uploads/'.$name]);
      }
    }
  }
  $saved=true;
}

$defaults=[
 'home_heading'=>'Apna behtareen rishta dhoondhiye','home_subtitle'=>'Hazaron profiles mein apna perfect match paaiye','home_online_count'=>'24+','home_note'=>'✨ Aaj naye profiles online hain — register karke matches dekhein','home_slider_interval'=>'4000',
 'female_banner_title'=>'स्वागत है','female_banner_subtitle'=>'आपका रजिस्ट्रेशन सफलतापूर्वक हो गया है।','female_banner_image'=>'https://randomuser.me/api/portraits/women/44.jpg',
 'female_earning_title'=>'Hamare Sath Judkar','female_earning_subtitle'=>'30 min Video Calling करके Extra Earning करें'
];
$bannerDefaults=[
 1=>['active'=>'1','title'=>'Apna behtareen rishta dhoondhiye','subtitle'=>'Hazaron verified profiles mein apna perfect match paaiye','image'=>'https://randomuser.me/api/portraits/women/44.jpg','button'=>'Abhi Register Karein','link'=>'#register'],
 2=>['active'=>'1','title'=>'Aapke shehar mein naye profiles','subtitle'=>'Online profiles dekhiye aur apni pasand ka rishta chuniye','image'=>'https://randomuser.me/api/portraits/women/68.jpg','button'=>'Profiles Dekhein','link'=>'#register'],
 3=>['active'=>'1','title'=>'Safe, Simple & Private','subtitle'=>'Apni details bhar kar matching profiles se judiye','image'=>'https://randomuser.me/api/portraits/women/79.jpg','button'=>'Join Now','link'=>'#register']
];
function hs($k){global $defaults; return h(setting($k,$defaults[$k]??''));}
function hb($i,$field){global $bannerDefaults; return h(setting("home_banner_{$i}_{$field}",$bannerDefaults[$i][$field]??''));}
?><!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/style.css"></head><body><main class="phone">
<header><a href="index.php">←</a><b>Homepage Settings</b></header>
<section class="card"><p style="color:#666">Homepage ka hero slider, text, online count aur profile cards yahin se edit karein.</p>
<?php if(isset($saved)):?><div class="success">Homepage settings updated.</div><?php endif;?>
<form method="post" enctype="multipart/form-data">
<h3>🖼️ Hero Banner Slider</h3>
<p class="admin-help">Homepage ke top par 3 banners slider mein chalenge. Image ke liye URL paste kar sakte hain ya image upload kar sakte hain.</p>
<?php for($i=1;$i<=3;$i++):?><div class="admin-banner-box"><h4>Banner <?=$i?></h4>
<label><input type="hidden" name="home_banner_<?=$i?>_active" value="0"><input type="checkbox" name="home_banner_<?=$i?>_active" value="1" <?=hb($i,'active')==='1'?'checked':''?>> Active</label>
<input name="home_banner_<?=$i?>_title" value="<?=hb($i,'title')?>" placeholder="Banner heading">
<input name="home_banner_<?=$i?>_subtitle" value="<?=hb($i,'subtitle')?>" placeholder="Banner subtitle">
<input name="home_banner_<?=$i?>_image" value="<?=hb($i,'image')?>" placeholder="Banner image URL (https://...) ">
<input type="file" name="home_banner_<?=$i?>_upload" accept="image/*">
<input name="home_banner_<?=$i?>_button" value="<?=hb($i,'button')?>" placeholder="Button text">
<input name="home_banner_<?=$i?>_link" value="<?=hb($i,'link')?>" placeholder="Button link e.g. #register">
</div><?php endfor;?>
<label>⏱️ Slider speed (milliseconds)</label><input name="home_slider_interval" type="number" min="2000" max="15000" value="<?=hs('home_slider_interval')?>">
<h3>🏠 Homepage Text</h3>
<input name="home_heading" value="<?=hs('home_heading')?>" placeholder="Main heading">
<input name="home_subtitle" value="<?=hs('home_subtitle')?>" placeholder="Subtitle">
<input name="home_online_count" value="<?=hs('home_online_count')?>" placeholder="Online count e.g. 24+">
<input name="home_note" value="<?=hs('home_note')?>" placeholder="Bottom banner text">
<h3>👩 Profile Cards</h3>
<?php for($i=1;$i<=6;$i++):?><div style="padding:10px 0;border-bottom:1px solid #eee"><b>Profile <?=$i?></b><input name="home_profile_<?=$i?>_name" value="<?=hs('home_profile_'.$i.'_name')?>" placeholder="Name"><input name="home_profile_<?=$i?>_city" value="<?=hs('home_profile_'.$i.'_city')?>" placeholder="City"><input name="home_profile_<?=$i?>_photo" value="<?=hs('home_profile_'.$i.'_photo')?>" placeholder="Photo URL (https://...) "></div><?php endfor;?>
<h3>👩 Female Home Banner</h3>
<input name="female_banner_title" value="<?=hs('female_banner_title')?>" placeholder="Banner title"><input name="female_banner_subtitle" value="<?=hs('female_banner_subtitle')?>" placeholder="Banner subtitle"><input name="female_banner_image" value="<?=hs('female_banner_image')?>" placeholder="Banner image URL"><input type="file" name="female_banner_upload" accept="image/*">
<h3>📹 Earning Card</h3>
<input name="female_earning_title" value="<?=hs('female_earning_title')?>" placeholder="Earning title"><input name="female_earning_subtitle" value="<?=hs('female_earning_subtitle')?>" placeholder="Earning subtitle">
<button>Save Homepage</button></form></section></main><script src="/rishtewale-protection.js"></script></body></html>

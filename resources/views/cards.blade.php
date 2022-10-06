<!DOCTYPE html  dir="rtl" lang="ar">
<html  dir="rtl" lang="ar">
<head>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="custom.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<style>
    .main-section{
    background-color: #f1f1f1;
    padding: 20px;

}
.card-part{
    border-radius: 5px;
    margin:0px;
    border:1px solid #DDDDDD;
    background-color: #fff;
    padding-bottom: 20px;
    padding-top: 20px;
    background-image: url("http://185.200.36.34:8010/storage/image_post/card.png") !important;
    height 100px;
    width: 100%;
    background-size: 500px !important ;

}
.card-title {
    margin:0px;
    color:#4C4C4C;
    font-size:25px;
    padding: 0px;
    margin:0px;
    text-align: justify;
}
.card-description p{
    min-height:10px;
    overflow: hidden;
    color:#848484;
    margin:0px;
    text-align: justify;
}
.card-cart a{
    border-radius:0px;
    font-size: 11px;
}
.card-cart{
    padding-top: 10px;


}
.avatar {
  vertical-align: middle;
  width: 300px;
  height: 300px;
  border-radius: 50%;
}
.h55{
    font-size: 20px;
    padding-right: 15px;
    color: #24327d !important ;

}
.diiv{
height: 100;
width: 50;
}
@media print {
   body {
      -webkit-print-color-adjust: exact;
   }
}
.row{
    height: 350;
}
#h{
    padding-top: 30px;
}
</style>
<body>
    @php
        $i=0;
    @endphp
<div class="container main-section">
	<div class="row">
        @foreach($userPrint as $user)
         @php
             $i++;
         @endphp
		<div class="col-md-6 col-sm-6 col-xs-6 card-main-section">
			<div class="row card-part">
                <div class="diiv" ></div>
				<div class="col-md-12 col-sm-12 col-xs-12 card-description">
					<h5 class="h55" id="h">اسم المستخدم :{{ $user->name }}</h5>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12 card-cart">
					<h5 class="h55" >كلمة السر :{{$user->text_plane}}</h5>
				</div>

			</div>
		</div>
        @if ($i==6)
        <p style="page-break-after: always"></p>
        @endif
        @php
            if($i==6){
                $i=0;
           }
        @endphp
        @endforeach
</div>
</div>
</body>
</html>

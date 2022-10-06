


<style>
    :root {
	--primary-color: #00bcd4;
	--accent-color: #f50057;

	--text-color: #263238;
	--body-color: #80deea;
	--main-font: 'roboto';
	--font-bold: 700;
	--font-regular: 400;
}
* { box-sizing: border-box }

body {
	color: var(--text-color);
	background-color: var(--body-color);
	font-family: var(--main-font), Arial;
	font-weight: var(--font-regular);
}
main{
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	width: 100vw;
	height: 100vh;
}
h1 { font-weight: var(--font-bold) }
input,
button {
	border: none;
	background: none;
	outline: 0;
}
button {cursor: pointer}
.SearchBox-input::placeholder {/* No es un seudoelemento estandar */
 color:white;
	opacity: .6;
}
/* Chrome, Opera ySafari */
.SearchBox-input::-webkit-input-placeholder {
  color: white;
}
/* Firefox 19+ */
.SearchBox-input::-moz-placeholder {
  color: white;
}
/* IE 10+ y Edge */
.SearchBox-input:-ms-input-placeholder {
  color: white;
}
/* Firefox 18- */
#formGroupExampleInput:-moz-placeholder {
  color: white;
}



.SearchBox {
	--height: 4em;
	display: flex;

	border-radius: var(--height);
	background-color: var(--primary-color);
	height: var(--height);
}
	.SearchBox:hover .SearchBox-input {
		padding-left: 2em;
		padding-right: 1em;
		width: 240px;
	}
	.SearchBox-input {
		width: 0;
		font-size: 1.2em;
		color: #fff;
		transition: .45s;
	}
	.SearchBox-button {
		display: flex;
		border-radius: 50%;
		width: var(--height);
		height: var(--height);
		background-color: var(--accent-color);
		transition: .3s;
	}
	.SearchBox-button:active  {
		transform: scale(.85);
	}
	.SearchBox-icon {
		margin: auto;
		color: #fff;
	}


@media screen and (min-width: 400px){
	.SearchBox:hover .SearchBox-input {
		width: 500px;
	}
}
</style>


<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!DOCTYPE html   dir="rtl" lang="ar">
<html lang="en"   dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>بحث مدرسة دريم</title>
</head>
<body>

    <main>
        <h1>مدارس دريم النموذجية</h1>
        <form class="card card-md" action="{{route('searchId')}}" autocomplete="off">
        <div class="SearchBox">
            <input type="text" class="SearchBox-input" name="id" placeholder="أدخل رقم المستخدم للبحث">
                <button type="submit" class="SearchBox-button">
                    <i class="SearchBox-icon  material-icons">search</i>
                </button>

        </div>
    </form>
    </main>

</body>
</html>


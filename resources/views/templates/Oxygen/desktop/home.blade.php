<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Mobile App</title>
</head>
<body>

    <tr>
		{% for user in Author_Name %}
		    {{ user.id }} - {{ user.name }}
		{% endfor %}
    </tr>



</body>
</html>
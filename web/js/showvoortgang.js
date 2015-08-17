function showDetails(v)
{
    var id = v + "hidden";
    if( document.getElementById(id).style.display=='none' )	{
        document.getElementById(id).style.display = '';
        document.getElementById(id).style.transition = 'all 10s ease';
    }
    else{
        document.getElementById(id).style.display = 'none';
        document.getElementById(id).style.transition = 'all 10s ease';
    }
}
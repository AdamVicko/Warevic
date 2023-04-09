//KOncentratorKisika
$( '#uvjetKisik' ).autocomplete(
    {
        source: function(req,res)
        {
            $.ajax(
            {
                url: url + 'koncentratorKisika/ajaxSearch/' + req.term,
                success:function(odgovor)
                {
                    res(odgovor);
                    //console.log(odgovor);
                }
            }); 
        },
        minLength: 2,
        select:function(dogadaj,ui)
        {
            //console.log(ui.item);
            spremi(ui.item);
        }
    })
    .autocomplete( 'instance' )._renderItem = function( ul, item )
{
    return $( '<li>' )
    .append( '<div>' + item.serijskiKod + '<div>')
    .appendTo( ul );
};

function spremi(koncentratorKisika)
{
    $.ajax({
        url: url + 'isporuka/dodajKoncentratorKisika?isporuka=' + isporukasifra + 
             '&koncentratorKisika=' + koncentratorKisika.sifra,
        success:function(odgovor){
            if(odgovor.error){
                $('#poruka').css('border','2px solid red');
                $('#poruka').html(odgovor.description);
                $('#poruka').fadeIn();
                setTimeout(() => {
                    $('#poruka').css('border','0px');
                    $('#poruka').fadeOut();
                }, 1500);
                //alert(odgovor.description);
                return;
            }
            $('#poruka').html(odgovor.description);
            $('#poruka').fadeIn();
                setTimeout(() => {
                    $('#poruka').css('border','0px');
                    $('#poruka').fadeOut();
                }, 1500);
                //debugger;
           $('#podaciKisik').append( // podaci za pacijenta html id
            '<tr>' + 
                '<td>' +
                    koncentratorKisika.serijskiKod +
                '</td>' + 
                '<td>' +
                    '<a href="#" class="odabraniKoncentratorKisika" id="p_' + 
                    koncentratorKisika.sifra
                    + '">' +
                    ' <i class="bi bi-trash3"></i>' +
                    '</a>' +
                '</td>' + 
            '</tr>'
           );
           definirajBrisanje();

     }
    }); 
}

//Pacijent
$( '#uvjetPacijent' ).autocomplete(
    {
        source: function(req,res)
        {
            $.ajax(
            {
                url: url + 'pacijent/ajaxSearch/' + req.term,
                success:function(odgovor)
                {
                    res(odgovor);
                    //console.log(odgovor);
                }
            }); 
        },
        minLength: 2,
        select:function(dogadaj,ui)
        {
            //console.log(ui.item);
            spremi(ui.item);
        }
    })
    .autocomplete( 'instance' )._renderItem = function( ul, item )
{
    return $( '<li>' )
    .append( '<div>' + item.imeprezime + '<div>')
    .appendTo( ul );
};

function spremi(pacijent)
{
    $.ajax({
        url: url + 'isporuka/dodajPacijenta?isporuka=' + isporukasifra + 
             '&pacijent=' + pacijent.sifra,
        success:function(odgovor){
            if(odgovor.error){
                $('#poruka').css('border','2px solid red');
                $('#poruka').html(odgovor.description);
                $('#poruka').fadeIn();
                setTimeout(() => {
                    $('#poruka').css('border','0px');
                    $('#poruka').fadeOut();
                }, 1500);
                //alert(odgovor.description);
                return;
            }
            $('#poruka').html(odgovor.description);
            $('#poruka').fadeIn();
                setTimeout(() => {
                    $('#poruka').css('border','0px');
                    $('#poruka').fadeOut();
                }, 1500);
                //debugger;
           $('#podaciPacijent').append( // podaci za pacijenta html id
            '<tr>' + 
                '<td>' +
                    pacijent.imeprezime +
                '</td>' + 
                '<td>' +
                    '<a href="#" class="odabraniPacijent" id="p_' + 
                    pacijent.sifra
                    + '">' +
                    ' <i class="bi bi-trash3"></i>' +
                    '</a>' +
                '</td>' + 
            '</tr>'
           );
           definirajBrisanje();

     }
    }); 
}



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
            spremiKisik(ui.item);
        }
    })
    .autocomplete( 'instance' )._renderItem = function( ul, item )
{
    return $( '<li>' )
    .append( '<div>' + item.serijskiKod + '<div>')
    .appendTo( ul );
};

function spremiKisik(koncentratorKisika)
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
                alert(odgovor.description);
                return;
            }
            $('#poruka').html(odgovor.description);
            $('#poruka').fadeIn();
                setTimeout(() => {
                    $('#poruka').css('border','0px');
                    $('#poruka').fadeOut();
                }, 1500);
                //debugger;
           $('#podaciKisik').append( // podaci za kisik html id
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
           definirajBrisanjeKisik();

     }
    }); 
}

function definirajBrisanjeKisik(){ // stavljamo u funkciju radi optimizranja rad znaci da odma povuce sifru da mozemo obrisat bez potrebe za refresh
    $('.odabraniKoncentratorKisika').click(function(){

        //console.log(isporukasifra);
        //console.log($(this).attr('id').split('_')[1]);
        let element = $(this);
        $.ajax({
            url: url + 'isporuka/obrisiKoncentratorKisika?isporuka=' + isporukasifra + 
                 '&koncentratorKisika=' + element.attr('id').split('_')[1],
            success:function(odgovor){
               element.parent().parent().remove();
         }
        }); 
    
        return false;
    });
}
definirajBrisanjeKisik();
$('#poruka').fadeOut();

$('#uvjet').focus();



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
            console.log(ui.item);
            spremiPacijent(ui.item);
        }
    })
    .autocomplete( 'instance' )._renderItem = function( ul, item )
{
    return $( '<li>' )
    .append( '<div>' + item.imeprezime + '<div>')
    .appendTo( ul );
};

function spremiPacijent(pacijent)
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
           definirajBrisanjePacijent();

     }
    }); 
}

function definirajBrisanjePacijent(){ // stavljamo u funkciju radi optimizranja rad znaci da odma povuce sifru da mozemo obrisat bez potrebe za refresh
    $('.odabraniPacijent').click(function(){

        console.log(isporukasifra);
        
        //console.log($(this).attr('id').split('_')[1]);
        let element = $(this);
        $.ajax({
            url: url + 'isporuka/obrisipacijent/' + isporukasifra ,
            success:function(odgovor){
               element.parent().parent().remove();
         }
        }); 
    
        return false;
    });
}
definirajBrisanjePacijent();
$('#poruka').fadeOut();



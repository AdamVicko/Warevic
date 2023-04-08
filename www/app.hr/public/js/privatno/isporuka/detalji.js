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
        url: url + 'isporuka/dodajpolaznik?grupa=' + grupasifra + 
             '&polaznik=' + polaznik.sifra,
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
           $('#podaci').append(
            '<tr>' + 
                '<td>' +
                    polaznik.ime + ' ' + polaznik.prezime +
                '</td>' + 
                '<td>' +
                    '<a href="#" class="odabraniPolaznik" id="p_' + 
                    polaznik.sifra
                    + '">' +
                    ' <i class="fi-trash"></i>' +
                    '</a>' +
                '</td>' + 
            '</tr>'
           );
           definirajBrisanje();

     }
    }); 
}

  var availableTags = [
    "ActionScript",
    "AppleScript",
    "Asp",
    "BASIC",
    "C",
    "C++",
    "Clojure",
    "COBOL",
    "ColdFusion",
    "Erlang",
    "Fortran",
    "Groovy",
    "Haskell",
    "Java",
    "JavaScript",
    "Lisp",
    "Perl",
    "PHP",
    "Python",
    "Ruby",
    "Scala",
    "Scheme"
  ];
  $( "#uvjetKisik" ).autocomplete({ // kacim ga na uvjet jer mi je serch id na vievu uvjet
    source: availableTags
  });
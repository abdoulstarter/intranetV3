
/*
 * *
 *  *  Copyright (C) $month.$year | David annebicque | IUT de Troyes - All Rights Reserved
 *  *
 *  *
 *  * @file /Users/davidannebicque/htdocs/intranetv3/assets/js/partials/admEdt.js
 *  * @author     David annebicque
 *  * @project intranetv3
 *  * @date 3/30/19 12:11 PM
 *  * @lastUpdate 3/30/19 12:11 PM
 *  *
 *
 */

let Cells = []
let Ind = 0

const $contextMenuEdt = $('#contextMenuEdt')
let $rowClicked

//menu contextuel
$(document).on("contextmenu", ".edt_cours", function (e) {
  $rowClicked = $(this);
  $contextMenuEdt.css({
    display: "block",
    position: "absolute",
    left: e.pageX,
    top: e.pageY
  });

  return false;
});

$contextMenuEdt.on("click", "a", function () {
  switch ($(this).data("priority")) {
    case 'suppr':
      $.ajax(
        {
          //url: "{{ path('da_kernel_administration_edt_suppr') }}",
          type: 'DELETE',
          data: 'id=' + $rowClicked[0].id,
          //dataType: "json", //Return data type (what we expect).
          success: function () {
            $('#' + $rowClicked[0].id).empty();

            addCallout('Element supprimé du planing.','success');
          },
          error: function () {
            addCallout('Erreur lors de la suppression.','error');
          }
        });
      break;
    case 'modifier':
      console.log('modification ' + $rowClicked[0].id);
      $('#blocadd').hide();
      const tabetu = $('#zoneaction')
      tabetu.empty().hide();
      /*tabetu.load("{{ path('da_kernel_administration_edt_modifier') }}", {
        id: $rowClicked[0].id,
        annee: $annee
      });*/
      tabetu.fadeIn(1000);
      break;
    case 'dupliquer':

      break;
    case 'deplacer':

      break;
  }

  $contextMenuEdt.hide();
  $('#load-page').hide();
});

$(document).click(function () {
  $contextMenuEdt.hide();
});

//une fois la selection terminée
function selectfin() {
  let valeur;

  let tabdbt = Cells[0].split('_')
  $('#hdbt').selectpicker('val', tabdbt[1]);

  let tabfin = Cells[Cells.length - 1].split('_')

  let fin = parseInt(tabfin[1]) + 1
  $('#hfin').selectpicker('val', fin);

  $('#jourc' + tabdbt[2]).prop("checked", true);

  let diff = parseInt(tabfin[3]) - parseInt(tabdbt[3])
  if (diff === 0)//donc sur la même colonne
  {
    valeur = 'TP-' + tabdbt[3]
  }
  else if (diff === 1)//donc sur 2 colonnes)
  {
    valeur = 'TD-' + tabdbt[3]
  }
  else {
    valeur = 'CM-1'
  }

  $('#typecours').selectpicker('val', valeur);
  $('#salle').selectpicker('val', '');
  $('#texte').selectpicker('val', '');

  $('#blocadd').show()
}

//au debut de la selection
function debut() {
  Cells = [];
  Ind = 0;
}


  //mémoriser les celulles selectionnées
  $('#selectable').selectable({
    filter: 'th,td:not(.modedt)',
    start: function (event, ui) {
      debut()
    },
    stop: function (event, ui) {
      selectfin()
    },
    selected: function (event, ui) {
      let s = $(this).find('.ui-selected');
      Cells[Ind] = $(ui.selected).attr('id');
      Ind = Ind + 1;

    }
  });


  //suppression d'une semaine
  $('#supprimerSemaineModal').click(function () {
    const $id = createModal('suppr')
    const $valeur = $(this).data('element')

    $('#textemodal_' + $idModal).html("Vous allez la semaine/promo suivante : \"" + $(this).attr('data-titre') + "\" .");

    $('#validSuppr-' + $id).click(function () {
      $.ajax(
        {
          url: Routing.generate('da_kernel_administraton_edt_semaine_suppr'),
          type: 'DELETE',
          data: 'id=' + $valeur,
          success: function (data) {
            closeModal();
            autohidenotify('success', 'La suppression a été effectuée avec succés !');

          },
          error: function () {
            closeModal();
            autohidenotify('error', 'Erreur lors la suppression.');
          }
        });
    });
  });

  $('#foc').scroll();

  //importer semaine
  $(document).on('click', '#idimportsemaine', function () {
    console.log('ok')
    const za = $('#zoneaction')
    za.empty()
    za.load(Routing.generate('administration_edt_za_importsemaine'))
    za.fadeIn(1000);
  });

  //zones
  $('#idcreerzone').click(function () {
    const za = $('#zoneaction')
    za.empty();
    //za.load("{{ path('da_kernel_administration_edt_creerzone') }}");
    za.fadeIn(1000);
  });

  //export
  $('#idexport').click(function () {
    const za = $('#zoneaction')
    za.empty();
    //za.load("{{ path('da_kernel_administration_edt_za_export') }}");
    za.fadeIn(1000);
    $('#load-page').hide();

  });

  //suppr semestre
  $('#ideffacer').click(function () {
    const za = $('#zoneaction')
    za.empty();
    //za.load("{{ path('da_kernel_administration_edt_za_effacer') }}");
    za.fadeIn(1000);
    $('#load-page').hide();

  });


$(document).on('click', '.closeza', function (e) {
  e.preventDefault();
  const za = $('#zoneaction')
  za.fadeOut(1000);
  za.empty();
  za.hide();
});

//changement d'heure de début
$(document).on('change', "#hdbt", function () {
  const t = parseInt($(this).val()) + 3
  $('#hfin').val(t);
});

//affiche par prof
$(document).on('change', '#afficheenseignant', function () {
  const tabetu = $('#zone_edt')
  const $sem = $('#semainereelle').val()

  tabetu.empty();
  /*tabetu.load("{{ path('da_kernel_administration_ajaxedt') }}", {
    filtre: 'prof',
    valeur: $(this).val(),
    semainer: $sem
  });*/
  tabetu.fadeIn(2000);
  $('#load-page').hide();

});

//affiche par prof
$(document).on('change', '#affichesalle', function () {
  const tabetu = $('#zone_edt')
  const $sem = $('#semainereelle').val()
  tabetu.empty().hide();
  /*tabetu.load("{{ path('da_kernel_administration_ajaxedt') }}", {
    filtre: 'salle',
    valeur: $(this).val(),
    semainer: $sem
  });*/
  tabetu.fadeIn(2000);
  $('#load-page').hide();
});


//affiche de tous les groupes de toutes les promos
$(document).on('change', '#affichejour', function () {
  const tabetu = $('#zone_edt')
  const $sem = $('#semainereelle').val()
  tabetu.empty();
  /*tabetu.load("{{ path('da_kernel_administration_ajaxedt') }}", {
    filtre: 'jour',
    valeur: $(this).val(),
    semainer: $sem
  });*/
  tabetu.fadeIn(2000);
  $('#load-page').hide();

});

//affichage d'une promo sur une semaine précise
$(document).on('change', '#affichesemaine', function () {
  const tabetu = $('#zone_edt')

  tabetu.empty();
  let $t = $(this).val().split('_');
  //tabetu.load("{{ path('da_kernel_administration_ajaxedt') }}", {filtre: $t[0], valeur: $t[1], semainer: $t[2]});
  $('#load-page').hide();
});

//affichage par matière
$(document).on('change', '#affichemodule', function () {
  const tabetu = $('#zone_edt')
  const $sem = $('#semainereelle').val()

  tabetu.empty();
  /*tabetu.load("{{ path('da_kernel_administration_ajaxedt') }}", {
    filtre: 'module',
    valeur: $(this).val(),
    semainer: $sem
  });*/
  tabetu.fadeIn(2000);
  $('#load-page').hide();

});

/***************/
/* EDT REALISE */
/***************/


$(document).on('change','#selectpersonnel', function () {
  const selectSemestre = $('#selectsemestre');
  const selectMatiere = $('#selectmatiere');

  selectSemestre.val('0');
  selectMatiere.selectpicker('destroy')
  selectMatiere.empty()
  selectMatiere.append(new Option("Choisissez un semestre !", "0"));
  selectMatiere.selectpicker('val', '0')

})

$(document).on('change','#edtSelectSemestre', function () {
  $.ajax(
    {
      url: Routing.generate('api_matieres_semestre_personnel', {semestre: $(this).val(), personnel: $('#selectpersonnel').val()}),
      type: 'POST',
      dataType: "json", //Return data type (what we expect).
      success: function (data) {
        const selectMatiere = $("#selectmatiere");
        selectMatiere.selectpicker('destroy')
        selectMatiere.empty()
        selectMatiere.append(new Option("Choisissez une matière !", ""))
        for (let key in data) {
          let value = data[key];
          console.log(key)
          console.log(value)
          selectMatiere.append(new Option(value.libelle + " (UE: " + value.ue + ")", value.id));
        }
        selectMatiere.selectpicker()
      },
      error: function () {

      }
    });
})

$(document).on('click', '#btnafficheRealise', function (e) {
  e.preventDefault();
  const $bloc = $('#blocchrono');
  $bloc.empty();
  $bloc.load(Routing.generate('administration_edt_service_realise_affiche', {
    semestre: $('#edtSelectSemestre').val(),
    personnel: $('#selectpersonnel').val(),
    matiere: $('#selectmatiere').val()
  }))
})

// $(document).ajaxComplete(function (event, xhr, settings) {
//   // actions
//
//
//   $('#selectmatiere').select2();
//   $('#selectenseignant').select2();
//   $('#typecours').select2();
//
//   //bouton journée entière
//   $("#allday").on("ifChecked ifUnchecked", function (event) {
//     if (event.type == 'ifChecked') {
//       var hdbt = $("#hdbt");
//       hdbt.prop('disabled', 'disabled');
//       hdbt.val(1);
//
//       var hfin = $("#hfin");
//       hfin.prop('disabled', 'disabled');
//       hfin.val(26);
//     }
//     else {
//       $("#hdbt").removeAttr('disabled');
//       $("#hfin").removeAttr('disabled');
//
//     }
//   });
//
//   $('.select2').select2();
//
// });


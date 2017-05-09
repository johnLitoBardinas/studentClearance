<script>
    //cache
        //var $table = $("#tblSec1");
        var $Modal = $("#myModal");
        var $ModalTitle = $("h4.modal-title").find('b'); 
        var $FormSec = $("form#secForm");
        var $TblsecSub = $("table#tblSecSub");

    //form Var 
        var Section_code, Track, Strand, Adviser, Room_Number, Shift_Sched, HS_Type, Grade_level; 

    //ajax Var
        var url;
        var method;
        var data;

    //section Var
        var FSubList = [];

        function addSection(){
            method = 'add';
            $FormSec[0].reset();
           $Modal.modal({ 
                backdrop:"static",
                keyboard:false
           });
           $("#secSubTbody").empty();
           $ModalTitle.text('New Section');
           $("button#btnSecSave").text('Save Section');

           $("select#secAdviser").empty();
           $("select#Subject_Code").empty();
           teacherList();
           subjectList();

            // disabled readonly property
            $FormSec.find("#secCode").removeAttr('readonly');
        }


        function toTitleCase(str)
        {
            return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
        }

        
        // Edit Section modal
        function editSec(secCode, TID){

          // empty the Subject list array
          var currnt_subList = [];

            method = 'edit';
            $FormSec[0].reset();
           
           $Modal.modal({ 
                backdrop:"static",
                keyboard:false
           });

           $ModalTitle.text('Edit Section');
           $("button#btnSecSave").text('Save Update');
           $("select#secAdviser").empty();
           $("select#Subject_Code").empty();
           teacherList();
           subjectList();
           $TblsecSub.find('tbody').empty();
           /*
            STEPs
            readonly lang dapat si sec Code 
              1. kukunin ko na si mga data na kailangan ng section from tbsection and tbsubject_section
              2. ilalagay eto mga fields kagaya ng ginawa sa teacher
              3. e uupdate eto or e sasave tapon sa controllerMod
           */
           url = "<?= site_url('index.php/AdminSection/getSectionSubjects') ?>/"+ secCode + "/" + TID;

          // empty the currentSubjects array
           //currnt_subList.splice(0, ctrSubList); 
           var opt;
           $.get(url, function(data){

              data = $.parseJSON(data);
             var fName = data[0].Teacher_Last_Name + ", " + data[0].Teacher_First_Name +" "+ data[0].Teacher_MiddleInitial;

             var tblSecSubList;

              //console.log(data);
              
              //for

              //alert(data.length);

              //console.log(data[0]);
              /* server data for the current section */


              Section_code = data[0].Section_code;
              Track = data[0].Track;
              Strand = data[0].Strand;
              Adviser = [data[0].Adviser, toTitleCase(fName)];
              Room_Number = data[0].Room_Number;
              Shift_Sched = data[0].Shift_Sched;
              HS_Type = data[0].HS_Type;
              Grade_level = data[0].Grade_level;

              $FormSec.find("#secCode").val(Section_code).attr('readonly', 'true');
              $FormSec.find("#secTrack").val(Track);
              $FormSec.find("#secStrand").val(Strand);
              $FormSec.find("#secRoom").val(Room_Number);

              opt = $('<option value="'+Adviser[0]+'" selected>'+Adviser[1]+'</option>');
              $FormSec.find('#secAdviser').prepend(opt);
              $FormSec.find("#secShift").val(Shift_Sched);
              $FormSec.find("#secGLvl").val(Grade_level);

              //opt = $('<option value="'+Adviser[0]+'" selected>'+Adviser[1]+'</option>');
              $FormSec.find("#secHSType").val(HS_Type);

              currnt_subList.push(Adviser[0]);

              for (var i = 1; i < data.length; i++) {
                  //console.log(data[i].Subject_Code + "index of " + i);

                  tblSecSubList = `
                  <tr>
                    <td>`+data[i].Subject_Code+`</td>
                    <td>`+fName+`</td>
                    <td>
                    <button type="button" class="btn btn-danger customBtn" id="removeSub"> <span class="fa fa-times"></span> Remove </button>
                    </td>
                  </tr>
                  `;

                  $TblsecSub.find('tbody').append(tblSecSubList);

                  console.log(data[i].Subject_Code + "index of " + i);
                  currnt_subList.push(data[i].Subject_Code);
              }

              //console.log("current subject with teacher");
              //console.log(currnt_subList);

              FSubList = currnt_subList;
              currnt_subList.splice(0, currnt_subList.length);
        });

        }

        //viewSubjects modal
        function secSubjects(secCode, TID){
            method = 'edit';
            $FormSec[0].reset();
           $Modal.modal({ 
                backdrop:"static",
                keyboard:false
           });
           $ModalTitle.text('Subject List');
        }

        // section adviser
        function teacherList() {
          var $secAdviser = $("select#secAdviser");
          url = "<?= site_url('index.php/AdminTeacher/ajax_teach_list') ?>";
          $.get(url, function(data){
/*            console.log(data); jsonString
*/            data = $.parseJSON(data);
            //console.log(data);
            var fName;
            var opt ;
            $.each(data.data, function(i, obj){

              fName = obj.Teacher_Last_Name + ", " + obj.Teacher_First_Name + " &nbsp " + obj.Teacher_MiddleInitial;
              opt += '<option value='+obj.Teacher_ID+' title="'+fName+'">'+fName+'</option>';
              //console.log(i,fName);
             
            }); 
            $secAdviser.append(opt);

          });

        }

        // list of available subjects
        function subjectList(){
          //getData
          //updateDom
          var $Subject_Code = $("select#Subject_Code");
          url = "<?= site_url('index.php/AdminSection/subjectList') ?>";

          $.get(url, function(data) {
            //console.log(data);
            data = $.parseJSON(data);
            //console.log(data);
            $.each(data.data, function(i, obj){
              var subCode = obj.Subject_Code;
              var opt = '<option value='+subCode+' title="'+obj.Subject_Description+'">'+subCode+'</option>';
              $Subject_Code.append(opt);
            });
          });
        }


        // addingSection Subjects New
        var subList = [];

        function addSecSub() {

          var curntSub = $("select#Subject_Code").val();
          var curntSubTitle = $("select#Subject_Code option:selected").attr('title');

          if(method == 'add'){
            //inserting new subjects
            subList.push(curntSub);
          }
          else{
            // adding new subject in section
            FSubList.push(curntSub);
          }

          var append = `
          <tr title="`+curntSubTitle+`">
            <td>`+curntSub+`</td>
            <td>`+$("select#secAdviser option:selected").text()+`</td>
            <td>
              <button type="button" class="btn btn-danger customBtn" id="removeSub"> <span class="fa fa-times"></span> Remove </button>
            </td>
          </tr>
          `;

          $TblsecSub.find('tbody').append(append);
        }


        //saving the section data
        function saveData() {
          // ibubutang so array sadi input hidden na subjects
          // kukuunon na so mga data sadi form dapat kaiba na so subjects na array
          // e coconsole log  na muna so mga data para mabayad iya
          

          if (method == 'add') {
            $('input[name="subjects[]"]').val(subList);
            var data = $FormSec.serializeArray();

            url = "<?= site_url('index.php/AdminSection/saveSection'); ?>/add";
            $.post(url, data, function(response){
              response = $.parseJSON(response);
              if(response.status){
                alert('Section Successfully Added!');
                $FormSec[0].reset();
                tblSecReload();
                $("#secSubTbody").empty();
              }
              
            });
          }else if(method == 'edit'){

            $('input[name="subjects[]"]').val(FSubList);
            var dataEdit = $FormSec.serializeArray();
            var editUrl = "<?= site_url('index.php/AdminSection/saveSection'); ?>/edit";

            //alert(method);
            //console.log(dataEdit);
            $.ajax({
                url: editUrl,
                type: 'POST',
                dataType: 'html',
                data: dataEdit,
            })
            .done(function(data) {
                //console.log("response from server edit section");
                //console.log(data);
                //alert(data);
                alert("Section Updated");
                tblSecReload();
            })
            .fail(function() {
                console.log("error");
            });

          }

        }

        //delete Section
        function delSec(secCode, teachId) {
          url = "<?= site_url('index.php/AdminSection/deleteSec') ?>/"+secCode+"/"+teachId;
          if (confirm('Delete the Section?')) {
            $.get(url, function(res){
              res = $.parseJSON(res);
              if (res.status) {
                alert('Record Deleted!');
                tblSecReload();
              }
            });
          }
        }


        //loadApp
        var app = {
            init:function(){
                $("#hrefSection").addClass('active');
                /*teacherList();
                subjectList();*/

                //delegation btnRemoveSubject
                $TblsecSub.unbind().on('click', 'button#removeSub', function(event){
                    event.preventDefault();
                    if (confirm("Remove Subject?")) {
                      $(this).closest('tr').fadeOut('slow');
                      var index = $(this).data('subCtr');
                      alert(index);  
                    }
                });

            }
        }

        //for datatable API 
        var table; 
        // docReady function
        $(document).ready(function(){
            //varApp Initialize
            app.init();

            //datatables
            table = $("table#tblSec1").DataTable({

                    /*key:value pairs_ JSON formated*/
                    "bInfo" : false,
                    "processing":true,
                    "serverSide":true,
                    "order":[],
                    "ajax":{

                        "url":"<?php echo site_url('index.php/AdminSection/ajax_sec_list') ?>",
                        "type":"POST"
                    },//ajax propeties with object JSON data


                    "columnDefs": [
                        { 
                            "targets": [ -1 ], //last column
                            "orderable": false, //set not orderable
                        }
                    ], //datatables colomDefinition

                    "columns":[
                        {"title":'Section ID '},
                        {"title":'Track'},
                        {"title":'Strand'},
                        {"title":'Adviser'},
                        {"title":'Room'},
                        {"title":'Grade'},
                        {"title":'Action'}
                    ]

                });

            });

        //datatables function
        //reloading the datatables
        function tblSecReload() {
          table.ajax.reload(null,false);
        }

    </script>
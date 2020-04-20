$(document).ready(function(){

    var source = $('#event-template').html();
    var eventTemplate = Handlebars.compile(source);

    $.each(events, function(index, event){
        var eventUI = eventTemplate(event) ;
        var date = event['date'];
        $('#calendar').find('.date-block[data-date="' + date +'"]').find('.events').append(eventUI);
    });

    var panel = {
        el: '#info-panel',
        selectedDateBlock: null,
        selectedEvent: null,
        init: function(isNew, e) {
            panel.clear();    //clear form data
            panel.updateDate(e);  //update panel的日期

            if(isNew) {
                $(panel.el).addClass('new').removeClass('update');
                panel.selectedDateBlock = $(e.currentTarget);   //將一開始點的date-block記錄(存)下來
            }
            else {
                $(panel.el).addClass('update').removeClass('new');
                panel.selectedDateBlock = $(e.currentTarget).closest('.date-block');
            }
        },
        clear: function(){
            $(panel.el).find('input').val('');
            $(panel.el).find('textarea').val('');
        },
        open: function(isNew, e) {
            panel.init(isNew, e);
            panel.hideError();
            $(panel.el).addClass('open').css({       //panel.el等同#info-panel
                top: e.pageY+'px',
                left: e.pageX+'px',
            }).find('.title [type]').focus();   //[]是找屬性
        },
        close: function() {
            $(panel.el).removeClass('open');
        },
        updateDate: function(e) {
            //get date from .data-block
            if ($(e.currentTarget).is('.date-block'))          //如果點到的是data-block
                var date = $(e.currentTarget).data('date');
            else
                var date = $(e.currentTarget).closest('.date-block').data('date');
            //get month from calendar
            var year = $('#calendar').data('year');
            var month = $('#calendar').data('month');

            $(panel.el).find('.month').text(month);             //panel介面顯示正確的月份和日期
            $(panel.el).find('.date').text(date);

            $(panel.el).find('[name="year"]').val(year);
            $(panel.el).find('[name="month"]').val(month);     //塞值/更新到input裡(存到form裡) 要用console.log來確認是否成功
            $(panel.el).find('[name="date"]').val(date);        //注意[]寫法
        },
        showError: function(msg) {
            $(panel.el).find('.error-msg').addClass('open')
                .find('.alert').text(msg);
        },
        hideError: function() {
            $(panel.el).find('.error-msg').removeClass('open');
        },
    };

    $('.date-block')
    .dblclick(function(e){
        panel.open(true, e);
    }).on('dblclick', '.event', function(e){
        e.stopPropagation();       //防止事件向父層傳遞 網頁的事件預設會一直往上傳播 date-block也有綁dblclick所以會觸發
        panel.open(false, e);     //開update的

        panel.selectedEvent = $(e.currentTarget);  //記錄點到的event, for delete用

        var id = $(this).data('id');
        //AJAX call -get event detail
        $.post('event/read.php', {id: id}, function(data, textStatus, xhr){

            $(panel.el).find('[name="id"').val(data.id);
            $(panel.el).find('[name="title"').val(data.title);
            $(panel.el).find('[name="start_time"').val(data.start_time);
            $(panel.el).find('[name="end_time"').val(data.end_time);
            $(panel.el).find('[name="description"').val(data.description);

        }).fail(function(xhr){
            panel.showError(xhr.responseText);
        });
        //load detail back to panel
    });

    $(panel.el)
    .on('click', 'button', function(e){
       if ($(this).is('.create') || $(this).is('.update')){
           if ($(this).is('.create')) {
                var action = 'event/create.php';
           }
           if ($(this).is('.update')) {
                var action = 'event/update.php';
           }
        //collect data
        var data = $(panel.el).find('form').serialize();   //一坨字串 網址?後面的 一次蒐集所有input資料
        
        $.post(action, data)
            .done(function (data, textStatus, jqXHR) {
                if ($(e.currentTarget).is('.update'))
                    panel.selectedEvent.remove();

                var eventUI = eventTemplate(data);    //生成完之後要插回去 插在哪? 用selectedDateBlock記錄當初點的是誰

                panel.selectedDateBlock.find('.event').each(function(index, event){
                    var eventFromTime = $(event).data('from').split(':');
                    var newEventFromTime = data.start_time.split(':');

                    if (eventFromTime[0]>newEventFromTime[0] ||
                        (eventFromTime[0]==newEventFromTime[0] && eventFromTime[1]>newEventFromTime[1])) {
                        $(event).before(eventUI);
                        return false;  //break的意思 若要continue 寫return
                    }
                });

                if (panel.selectedDateBlock.find('.event[data-id="'+data.id+'"]').length == 0)
                    panel.selectedDateBlock.find('.events').append(eventUI);

                panel.close();
            })
            .fail(function(xhr, textStatus, errorThrown){
                panel.showError(xhr.responseText);
            });
       }
       if ($(this).is('.cancel')){
            panel.close();
       }
       if ($(this).is('.delete')){
           var result = confirm('Do you want to delete?');
           if (result){
                var id = panel.selectedEvent.data('id');  //取得id
                     $.post('event/delete.php', {id : id})
                        .done(function(){
                            panel.selectedEvent.remove();  //remove event from calendar
                            panel.close();
                        });
           }
       }
    })
    .on('click', '.close', function(e){
        $('button.cancel').click();
    });
});

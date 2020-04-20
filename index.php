<?php include ('header.php') ?>
<?php include ('data.php') ?>
<?php include ('template.php') ?>
<?php
if ($_SESSION["loggedin"] == false){
  header("location: register.php");
  exit;
}
?>
<div id="calendar" data-year="<?= date('Y')?>" data-month="<?= date('m')?>">
    <div id="header" class="clearfix">
        <div id='date'><?= date('Y')?>/<?= date('m')?></div>
        <p id="log-out"><a href="logout.php">Log out</a></button>
    </div>

    <div id="days" class="clearfix">
        <div class="day">SUN</div>
        <div class="day">MON</div>
        <div class="day">TUE</div>
        <div class="day">WED</div>
        <div class="day">THR</div>
        <div class="day">FRI</div>
        <div class="day">SAT</div>
    </div>

    <div id="dates" class="clearfix">
        <?php foreach($dates as $key => $date): ?>
            <div class="date-block <?= (is_null($date))? 'empty': ''?>" data-date="<?= $date ?>">
                <div class="date"><?= $date ?></div>
                <div class="events">
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<div id="info-panel" class="new">
    <div class="close">X</div>
  <form>
      <input type="hidden" name="id">
      <div class="title">
          <label>event</label><br>
          <input type="text" name="title">
      </div>
      <div class="error-msg">
          <div class="alert alert-danger">error</div>
      </div>
      <div class="time-picker">
          <div class="select-date">
              <span class="month"></span>/<span class="date"></span>
              <input type="hidden" name="year">
              <input type="hidden" name="month">                 <!--非user輸入的 由程式決定 -->
              <input type="hidden" name="date">
          </div>
          <div class="from">
              <label for="from">from</label><br>
              <input type="time" id="from" name="start_time">
          </div>
          <div class="to">
              <label for="to">to</label><br>
              <input type="time" id="to" name="end_time">
          </div>
      </div>
      <div class="description">
          <label>description</label><br>
          <textarea name="description" id="description"></textarea>
      </div>
  </form>
  <div class="buttons clearfix" >
          <button class="create">create</button>
          <button class="update">update</button>
          <button class="cancel">cancel</button>
          <button class="delete">delete</button>
  </div>
</div>

<?php include ('footer.php') ?>

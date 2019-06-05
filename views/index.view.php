<?php require('partials/head.php');?>

<h2 class="mb-4">Intervals Application</h2>

<?php if(isset($params['request']['msg'])){ ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $params['request']['msg']; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php } ?>

<?php if(isset($params['request']['msg-danger'])){ ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo $params['request']['msg-danger']; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php } ?>

<?php if(isset($params['request']['edit'])){ ?>
  <h3 class="mt-4">Edit interval</h3>
  <form class="mb-5"  action="intervals" method="POST">
    <div class="form-row align-items-center">
      <div class="col-sm-3 my-1">
        <label class="sr-only" for="inlineFormInputName">Start Date</label>
        <input name='date_start' type="date" value="<?php echo $params['interval']->date_start; ?>" class="form-control"  placeholder="Select start date">
      </div>
      <div class="col-sm-3 my-1">
        <label class="sr-only" for="inlineFormInputGroupUsername">End Date</label>
         <input name='date_end' type="date" value="<?php echo $params['interval']->date_end; ?>" class="form-control"  placeholder="Select end date">
      </div>
      <div class="col-sm-3 my-1">
        <label class="sr-only" for="inlineFormInputName">Price</label>
        <input name='price' type="text" value="<?php echo $params['interval']->price; ?>" class="form-control" placeholder="Select price for the interval">
      </div>

      <div class="col-auto my-1">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" onclick="location.href='/intervals';" class="btn btn-danger">Cancel</button>
      </div>
    </div>
    <input type="hidden" name="id" value="<?php echo $params['request']['edit']; ?>"/>
  </form>
<?php } ?>

<table class="table table-hover table-sm">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Start</th>
      <th scope="col">End</th>
      <th scope="col">Price</th>
      <th scope="col">Options</th>
    </tr>
  </thead>
  <tbody>
    <?php
     foreach ($params['data'] as $interval) {
    ?>
    <tr>
      <th scope="row"><?php echo $interval->id; ?></th>
      <td><?php echo $interval->date_start; ?></td>
      <td><?php echo $interval->date_end; ?></td>
      <td>$<?php echo $interval->price; ?></td>
      <td>
        <div class"d-flex flex-row">

          <!-- Edit option -->
          <a href="intervals?edit=<?php echo $interval->id; ?>" >
            <i class="fas fa-pencil-alt ml-3 mr-3"></i>
          </a>

          <!-- Delete option -->
          <a href="intervals?delete=<?php echo $interval->id; ?>" >
            <i class="fas fa-trash-alt"></i>
          </a>

        </div>
      </td>
    </tr>
    <?php
    }
    ?>
  </tbody>
</table>


<h3 class="mt-4">Add new interval</h3>
<form action="intervals" method="POST">
  <div class="form-row align-items-center">
    <div class="col-sm-3 my-1">
      <label class="sr-only" for="inlineFormInputName">Start Date</label>
      <input name='date_start' type="date" class="form-control" id="inlineFormInputName" placeholder="Select start date">
    </div>
    <div class="col-sm-3 my-1">
      <label class="sr-only" for="inlineFormInputGroupUsername">EndD Date</label>
       <input name='date_end' type="date" class="form-control" id="inlineFormInputName" placeholder="Select end date">
    </div>
    <div class="col-sm-3 my-1">
      <label class="sr-only" for="inlineFormInputName">Price</label>
      <input name='price' type="text" class="form-control" id="inlineFormInputName" placeholder="Select price for the interval">
    </div>

    <div class="col-auto my-1">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </div>
</form>

<h3 class="mt-4">Delete all intervals</h3>
<!-- <button type="button"  class="btn btn-danger">Delete</button> -->

<!-- Button trigger modal -->
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
  Delete
</button>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModal">Action</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Do you really want to delete all the data
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abort</button>
        <button type="button" onclick="deleteAllAction()" class="btn btn-primary">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php require('partials/footer.php'); ?>

<script type="text/javascript">
  function deleteAllAction() {
    window.location = '/intervals?deleteAll=1'
  }
</script>

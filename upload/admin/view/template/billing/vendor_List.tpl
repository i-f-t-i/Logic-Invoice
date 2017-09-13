<?php echo $header; ?>
<ol class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
  <?php } ?>
</ol>
<?php if ($error_warning) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>
<?php if ($success) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="pull-right">
      <a href="<?php echo $insert; ?>" title="<?php echo $button_add; ?>" data-toggle="tooltip" class="btn btn-success"><i class="fa fa-plus"></i></a>
      <button type="button" title="<?php echo $button_delete; ?>" data-toggle="tooltip" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-vendor').submit() : false;"><i class="fa fa-trash"></i></button>
    </div>
    <h1 class="panel-title"><i class="fa fa-list fa-lg"></i> <?php echo $heading_title; ?></h1>
  </div>
  <div class="panel-body">
    <form method="post" action="<?php echo $delete; ?>" id="form-vendor">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <tr>
            <th class="text-center" width="1"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
            <th class="text-left"><?php if ($sort == 'name') { ?>
              <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
              <?php } ?></th>
              <th class="text-left"><?php if ($sort == 'address') { ?>
              <a href="<?php echo $sort_address; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_address; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_address; ?>"><?php echo $column_address; ?></a>
              <?php } ?></th>
              <th class="text-left"><?php if ($sort == 'email') { ?>
              <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
              <?php } ?></th>
              <th class="text-left"><?php if ($sort == 'phone') { ?>
              <a href="<?php echo $sort_phone; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_phone; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_phone; ?>"><?php echo $column_phone; ?></a>
              <?php } ?></th>
              <th class="text-right"><?php if ($sort == 'date_added') { ?>
              <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
              <?php } ?></th>
              <th class="text-right"><?php if ($sort == 'date_modified') { ?>
              <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
              <?php } ?></th>
            <th class="text-right"><?php echo $column_action; ?></th>
          </tr>
          <tr class="filter">
            <td></td>
            <td class="text-left"><input type="text" name="filter_name" value="<?php echo $filter_name; ?>" class="form-control input-sm" /></td>
            <td class="text-left"><input type="text" name="filter_address" value="<?php echo $filter_address; ?>" class="form-control input-sm" /></td>
            <td class="text-left"><input type="text" name="filter_email" value="<?php echo $filter_email; ?>" class="form-control input-sm" /></td>
            <td class="text-left"><input type="text" name="filter_phone" value="<?php echo $filter_phone; ?>" class="form-control input-sm" /></td>
            <td class="text-left"><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" class="form-control input-sm date" /></td>
            <td class="text-right"><input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" class="form-control input-sm date" /></td>
            <td class="text-right"><button type="button" title="<?php echo $button_search; ?>" data-toggle="tooltip" class="btn btn-primary btn-xs" onclick="filter();"><i class="fa fa-search"></i></button></td>
          </tr>
          <?php if ($vendors) { ?>
          <?php foreach ($vendors as $vendor) { ?>
          <tr>
            <td class="text-center"><?php if (in_array($vendor['vendor_id'], $selected)) { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $vendor['vendor_id']; ?>" checked="checked" />
              <?php } else { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $vendor['vendor_id']; ?>" />
              <?php } ?></td>
            <td class="text-left"><?php echo $vendor['name']; ?></td>
            <td class="text-left"><?php echo $vendor['address']; ?></td>
            <td class="text-left"><?php echo $vendor['email']; ?></td>
            <td class="text-left"><?php echo $vendor['phone']; ?></td>
            <td class="text-right"><?php echo $vendor['date_added']; ?></td>
            <td class="text-right"><?php echo $vendor['date_modified']; ?></td>
            <td class="text-right">
              
              <a href="<?php echo $vendor['edit']; ?>" title="<?php echo $button_edit; ?>" data-toggle="tooltip" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
            </td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
    </form>
    <?php echo $pagination; ?>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?load=billing/vendor&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

    var filter_address = $('input[name=\'filter_address\']').val();

	if (filter_address) {
		url += '&filter_address=' + encodeURIComponent(filter_address);
	}

	var filter_email = $('input[name=\'filter_email\']').val();

	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}

	var filter_phone = $('input[name=\'filter_phone\']').val();

	if (filter_phone) {
		url += '&filter_phone=' + encodeURIComponent(filter_phone);
	}

	var filter_date_added = $('input[name=\'filter_date_added\']').val();

	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}

	location = url;
}

$(document).ready(function () {
	$('.filter input').on('keydown', function (e) {
		if (e.keyCode == 13) {
			filter();
		}
	});

	$('.date').datetimepicker({
		format: 'YYYY-MM-DD'
	});
});
//--></script>
<?php echo $footer; ?>
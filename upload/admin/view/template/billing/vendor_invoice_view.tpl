<?php echo $header; ?>
<ol class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
  <?php } ?>
</ol>
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="pull-right">
     
      <a href="<?php echo $cancel; ?>" title="<?php echo $button_cancel; ?>" data-toggle="tooltip" class="btn btn-danger"><i class="fa fa-times"></i></a>
    </div>
    <h1 class="panel-title"><i class="fa fa-search"></i> <?php echo $heading_title; ?></h1>
  </div>
  <div class="panel-body">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
      <li><a href="#tab-payment-detail" data-toggle="tab"><?php echo $tab_payment_detail; ?></a></li>
      <li><a href="#tab-payment-method" data-toggle="tab"><?php echo $tab_payment_method; ?></a></li>
      <li><a href="#tab-item" data-toggle="tab"><?php echo $tab_item; ?></a></li>
   
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab-general">
        <table class="table table-striped table-bordered table-hover">
          <tr>
            <td><b><?php echo $entry_invoice_id; ?></b></td>
            <td><?php echo $invoice_id; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $entry_vendor; ?></b></td>
            <td><?php echo $name; ?></a></td>
          </tr>
             <tr>
            <td><b><?php echo $entry_phone; ?></b></td>
            <td><?php echo $phone; ?></td>
          </tr>
             <tr>
            <td><b><?php echo $entry_address; ?></b></td>
            <td><?php echo $address; ?></td>
          </tr>
          
          <tr>
            <td><b><?php echo $entry_email; ?></b></td>
            <td><?php echo $email; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $entry_invoiceno; ?></b></td>
            <td><?php echo $invoiceno; ?></td>
          </tr>
         
          <tr>
            <td><b><?php echo $entry_date_due; ?></b></td>
            <td><?php echo $date_due; ?></td>
          </tr>
        </table>
      </div>
      <div class="tab-pane" id="tab-payment-detail">
        <table class="table table-striped table-bordered table-hover">
          
          <tr>
            <td><b><?php echo $entry_payment_address; ?></b></td>
            <td><?php echo $payment_address; ?></td>
          </tr>
          
         
          <tr>
            <td><b><?php echo $entry_payment_email; ?></b></td>
            <td><?php echo $payment_email; ?></td>
          </tr>
          
         
          <tr>
            <td><b><?php echo $entry_payment_phone; ?></b></td>
            <td><?php echo $payment_phone; ?></td>
          </tr>
         
        </table>
      </div>
      <div class="tab-pane" id="tab-payment-method">
        <table class="table table-striped table-bordered table-hover">
          <?php if ($payment_code) { ?>
          <tr>
            <td><b><?php echo $entry_payment_code; ?></b></td>
            <td><?php echo $payment_code; ?></td>
          </tr>
          <?php } ?>
        
          <?php if ($payment_description) { ?>
          <tr>
            <td><b><?php echo $entry_payment_description; ?></b></td>
            <td><?php echo $payment_description; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><b><?php echo $entry_currency_code; ?></b></td>
            <td><?php echo $currency_code; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $entry_currency_value; ?></b></td>
            <td><?php echo $currency_value; ?></td>
          </tr>
        </table>
      </div>
      <div class="tab-pane" id="tab-item">
        <table class="table table-striped table-bordered table-hover">
          <tr>
            <th class="text-left"><?php echo $column_number; ?></th>
            <th class="text-left"><?php echo $column_title; ?></th>
            <th class="text-left"><?php echo $column_description; ?></th>
            <th class="text-left"><?php echo $column_quantity; ?></th>
            <th class="text-right"><span data-toggle="tooltip" title="<?php echo $tooltip_price; ?>"><?php echo $column_price; ?> <i class="fa fa-question-circle"></i></span></th>
            <th class="text-right"><?php echo $column_discount; ?></th>
            <th class="text-right"><?php echo $column_total; ?></th>
          </tr>
          <?php foreach ($items as $item) { ?>
          <tr>
            <td class="text-left"><?php echo $item['number']; ?></td>
            <td class="text-left"><?php echo $item['title']; ?></td>
            <td class="text-left"><?php echo $item['description']; ?></td>
            <td class="text-left"><?php echo $item['quantity']; ?></td>
            <td class="text-right"><?php echo $item['price']; ?></td>
            <td class="text-right"><?php echo $item['discount']; ?></td>
            <td class="text-right"><?php echo $item['total']; ?></td>
          </tr>
          <?php } ?>
          <?php foreach ($totals as $total) { ?>
          <tr>
            <td class="text-right" colspan="6"><b><?php echo $total['title']; ?></b></td>
            <td class="text-right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      
    </div>
  </div>
</div>

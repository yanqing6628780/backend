<div class="well">
	<p class="text-danger">
		现已购买<?=formatAmount($row['total']); ?>元 商品.方案要求最少购买额为 <?=formatAmount($RSTR['amount']); ?>	元
		<?php if($RSTR['amount']-$row['total'] > 0): ?>还差<?=formatAmount($RSTR['amount']-$row['total'])?> 元<?php endif; ?>
	</p>
</div>
<div class="row">
	<div class="col-md-6">
		<div id="RSTR-pie"></div>		
	</div>	
	<div class="col-md-6">
		<div id="order_class"></div>		
	</div>
</div>
<table class="table table-striped" >
	<thead>
		<tr>
			<th>订单编号</th>
			<th>总金额</th>
			<th>创建时间</th>
			<th>修改时间</th>
			<th>订单状态</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=$row['order_number']; ?></td>
			<td>￥<?=formatAmount($row['total']); ?>元</td>
			<td><?=$row['create_time']; ?></td>
			<td><?=$row['modify_time']; ?></td>
			<td><?=$row['is_pass'] ? '成功下单' : '未提交'; ?></td>
		</tr>
	</tbody>
</table>
<table class="table table-striped" >
	<thead>
		<tr>
			<th>图片</th>
			<th>宝贝名称</th>
			<th>类别</th>
			<th>款式</th>
			<th>单价</th>
			<th>数量</th>
			<th>小计</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($order_products as $key => $value): ?>
		<tr>
			<td><img width="100" src="<?=$value['info']['picture'] ? $value['info']['picture'] : 'http://placehold.it/100x100/999999'?>" alt="..."></td>
			<td><?=$value['info']['product_name']; ?></td>
			<td><?=$value['info']['small_class_name']; ?></td>
			<td><?=$value['style_num']; ?></td>
			<td>￥<?=formatAmount($value['info']['unit_price']); ?>元</td>
			<td><?=$value['sum_qty']; ?></td>
			<td>￥<?=formatAmount($value['info']['unit_price']*$value['sum_qty']); ?>元</td>
			<td><a data-trigger="ajax" href="<?=site_url('orders/id/'.$row['id'].'/'.$row['order_number'].'/?product_id='.$value['product_id'])?>" data-target="#main" class="btn btn-info" type="button">查看</a></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<script type="text/javascript" src="<?=site_url('js/spinner.min.js')?>"></script>
<script type="text/javascript">
$(function () {
	var pieData = [];
	<?php foreach ($RSTR['sc_limits'] as $key => $value):?>
	pieData.push([
		"<?=$value['small_class_name']?>",<?=$value['percentage']?>
	]);
	<?php endforeach;?>

	var classPieData = [];
	<?php foreach ($order_small_class_sum as $key => $value):?>
	classPieData.push([
		"<?=$value['small_class_name']?>",<?=$value['sum_qty']?>
	]);
	<?php endforeach;?>
	

	var chartOptions = {
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false
	}
	$('#order_class').highcharts({
		chart: chartOptions,
		title: {
			text: '订单各类别所占比例'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.y}件</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				}
			}
		},
		series: [{
			type: 'pie',
			name: '数量',
			data: classPieData
		}]
	});
	$('#RSTR-pie').highcharts({
		chart: chartOptions,
		title: {
			text: '方案要求类别所占比例'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.y:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					format: '<b>{point.name}</b>: {point.y:.1f} %'
				}
			}
		},
		series: [{
			type: 'pie',
			name: '占比',
			data: pieData
		}]
	});
});
</script>
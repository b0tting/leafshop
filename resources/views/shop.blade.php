@extends('app')
@section('main')
			<div>
				<h1>Order</h1>
			</div>

			@foreach ($items as $item)
			<div class="row">
				<!-- Ik huil mijzelf in slaap om deze hack iedere nacht. Vijf minuten over live moet er plots halverwege dit in. Blergh.-->
				  <div class="col-md-4"><img src="images/{{{ $item->img_name }}}"/></div>
				  <div class="col-md-6">
					<div class="itemblock">
						<h2>{{{ $item->title }}}</h2>
						@if ($item->stock < 10 && $item->stock > 0)
							<span>&nbsp;(Only a few left!)</span>
						@endif
					</div>
					  <div class="itemblock">
						<h2>&euro; {{{ $item->price}}}</h2>
					</div>
					<div class="itemblock">
						<p><br>{!!  str_replace('[br]', '<br>', $item->description) !!}</p>
					</div>
					<div class="itemblock">
						@if ($item->stock > 0)
						<button class="btn btn-default btn-xs button_add" shippingfactor="{{$item->shippingfactor}}" itemprice="{{ $item->price }}" itemid="{{ $item->id }}" itemname="{{ $item->title }}">{{trans('shop.add')}} </button>
							@else
							<button disabled class="btn btn-default btn-xs button_add">Sold out!</button>
							@endif
					</div>					

				</div>
			</div>
			<div class="row">&nbsp;</div>
			@if ($item->id === 1)
				<div class="row">
					<div class="col-md-4"></div>
					<div class="col-md-6">
						<div class="itemblock">
							<h2>LEAF DIGITAL DOWNLOAD "LYS" 2015</h2>
						</div>
						<div class="itemblock">
							<p>This is the digital download for the new full length album of LEAF 'LYS'.
								Contains 10 new original tracks.
								Produced, recorded, mixed and mastered at LAVA studio (Denmark) with producer Christopher Juul.</p>
						</div>
						<div class="itemblock">You can find our digital downloads <a href="https://leafnordicfolk.bandcamp.com/" target="_blank">here</a></div>

					</div>
				</div>
				<div class="row">&nbsp;</div>
			@endif

				@endforeach
			<script language="JavaScript">
				state = "shop";
			</script>

@endsection
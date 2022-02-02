 @isset($breadcrumbs)
     <ol class="breadcrumb float-sm-right">
         <li class="breadcrumb-item"><a href="/">Home</a></li>
         @foreach ($breadcrumbs as $b)
             @if (!$loop->last)
                 <li class="breadcrumb-item">
                     <a href="{{ $b['href'] ?? '' }}">{{ $b['label'] }}</a>
                 </li>
             @else
                 <li class="breadcrumb-item active">{{ $b['label'] }}</li>
             @endif
         @endforeach
     </ol>
 @endisset

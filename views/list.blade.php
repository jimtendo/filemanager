<ol class="breadcrumb">
  <?php $breadcrumbs = preg_split('@/@', $fm->getCurrDir(), NULL, PREG_SPLIT_NO_EMPTY); ?>
  <li><a href="{{ $fm->actionUrl('list', '') }}">Root</a></li>
  @foreach ($breadcrumbs as $i=>$dir)
  <li class="{{ (count($breadcrumbs) == $i+1) ? 'active' : '' }}">
      <a href="{{ $fm->actionUrl('list', implode('/', array_slice($breadcrumbs, 0, $i))) }}">{{ $dir }}</a>
  </li>
  @endforeach
</ol>

<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th class="text-center" style="width:70%">Name</th>
      <th class="text-center" style="width:5%">Size</th>
      <th class="text-center" style="width:20%">Last Modified</th>
      <th class="text-center">Actions</th>
    </tr>
  </thead>
  <tbody>
  
    @foreach ($directories as $directory=>$meta)
    <tr>
      <td>
          <a href="{{ $fm->actionUrl('list', $meta['dir']) }}">
              <span class="glyphicon glyphicon-folder-close"></span> {{ $directory }}
          </a>
      </td>
      <td class="text-right">{{ $meta['size'] }}</td>
      <td class="text-center">{{ date('Y-m-d H:i:s', $meta['date']) }}</td>
      <td class="text-center">
          <a class="rename-button" title="Rename" data-name="{{ $directory }}"><span class="text-primary glyphicon glyphicon-edit"></span></a>
          <a href="{{ $fm->actionUrl('directory-delete', $fm->getCurrDir(), ['_name'=>$directory]) }}" title="Delete">
              <span class="text-danger glyphicon glyphicon-remove"></span>
          </a>
      </td>
    </tr>
    @endforeach
    
    @foreach ($files as $file=>$meta)
    <tr>
      <td>
          <a href="{{ $fm->actionUrl('download', $meta['dir'], ['_file'=>$file]) }}" target="_download">
              <span class="glyphicon glyphicon-file"></span> {{ $file }}
          </a>
      </td>
      <td class="text-right">{{ $meta['size'] }}</td>
      <td class="text-center">{{ date('Y-m-d H:i:s', $meta['date']) }}</td>
      <td class="text-center">
          <a class="rename-button" title="Rename" data-name="{{ $file }}"><span class="text-primary glyphicon glyphicon-edit"></span></a>
          <a href="{{ $fm->actionUrl('delete', $meta['dir'], ['_file'=>$file]) }}" title="Delete">
              <span class="text-danger glyphicon glyphicon-remove"></span>
          </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="row">
    <div class="col-sm-6">
        <form method="post" action="{{ $fm->actionUrl('upload', $fm->getCurrDir()) }}" enctype="multipart/form-data">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <input type="file" name="files[]" multiple="multiple">
            <button class="btn btn-primary" type="submit" style="margin-top:0.5em;">Upload</button>
        </form>
    </div>
    <div class="col-sm-6 text-right">
        <a class="dir-create-button"><span class="glyphicon glyphicon-folder-open"></span> Create New Folder</a>
    </div>
</div>

<script>
    $('#{{ $fm->getFMID() }} .rename-button').on("click", function() {
        var from = $(this).data('name');
        var to = window.prompt('Please enter new name...', from);
        
        if (to != null) {
            {{ $fm->getJsVarName() }}.load("{!! $fm->actionUrl('rename', $fm->getCurrDir()) !!}&_from=" + from + "&_to=" + to);
        } 
    });
    
    $('#{{ $fm->getFMID() }} .dir-create-button').on("click", function() {
        var dirName = window.prompt('New directory name');
        
        if (dirName != null) {
            {{ $fm->getJsVarName() }}.load("{!! $fm->actionUrl('directory-create', $fm->getCurrDir()) !!}&_name=" + dirName);
        } 
    });
</script>
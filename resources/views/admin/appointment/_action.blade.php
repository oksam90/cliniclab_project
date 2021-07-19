<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-cog"></i> {{__('Action')}}
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      @can('view_appointment')
          <a href="{{route('admin.appointment.show',$appointment['id'])}}" class="dropdown-item">
            <i class="fa fa-eye"></i> {{__('Show')}}
          </a>
      @endcan
      @can('edit_appointment')
          <a href="{{route('admin.appointment.edit',$appointment['id'])}}" class="dropdown-item">
            <i class="fa fa-edit"></i> {{__('Edit')}}
          </a>
      @endcan
      @can('delete_appointment')
          <form method="POST" action="{{route('admin.appointment.destroy',$appointment['id'])}}"  class="d-inline">
              <input type="hidden" name="_method" value="delete">
              <a href="#" class="dropdown-item delete_appointment">
                <i class="fa fa-trash"></i>
                {{__('Delete')}}
              </a>
          </form>
      @endcan
      @can('create_group')
          <a href="{{route('admin.appointment.create_tests',$appointment['id'])}}" class="dropdown-item">
            <i class="fa fa-flask"></i> {{__('Create group tests')}}
          </a>
      @endcan
    </div>
  </div>
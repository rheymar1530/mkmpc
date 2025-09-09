<aside class="main-sidebar sidebar-dark-primary elevation-4 cust-sidebar-color border-radius-xl" style="background:linear-gradient(to right, #232526, #414345) !important;color: white;">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link text-center">
      <!-- <img src="{{URL::asset('dist/img/LIBCAP_LOGO.jpg')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light">{{env('APP_NAME')}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar l-navbar" style="display: block; overflow-y: auto; max-height: calc(100vh - 120px);overflow-x:hidden;">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ MySession::myPhoto() }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ MySession::myName() }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu" data-accordion="false">

          @foreach(WebHelper::sidebarMenu(0) as $menu)
            <?php $href = (empty($menu->children))?" href= '$menu->path'":'';    ?>
            <li data-id='{{$menu->id}}' class='nav-item {{(!empty($menu->children))?"treeview":""}}'>
              <a <?php echo $href; ?> class="nav-link ">
                <i class="nav-icon {{ $menu->icon }}"></i>
                <p>
                  {{ $menu->name }}
                  <i class='right {{(!empty($menu->children))?"fas fa-angle-left":""}}'></i>
                </p>
              </a>
              @if(!empty($menu->children))
                <ul class="nav nav-treeview">
                  @foreach($menu->children as $child)
                    <li data-id='{{$child->id}}' class="nav-item">
                      <a  href="{{$child->path}}" class="nav-link">
                        <i class="{{ $child->icon }} child_nav"></i>
                        <p>{{ $child->name }}</p>
                      </a>
                    </li>
                  @endforeach 
                </ul>
              @endif
            </li>
          @endforeach

        <?php
          $report = WebHelper::sidebarMenu(0,1);
        ?>

        @if(count($report) > 0)
         <li class="nav-header">Reports</li>
          @foreach($report as $menu)
            <?php $href = (empty($menu->children))?" href= '$menu->path'":'';    ?>
            <li data-id='{{$menu->id}}' class='nav-item {{(!empty($menu->children))?"treeview":""}}'>
              <a <?php echo $href; ?> class="nav-link ">
                <i class="nav-icon {{ $menu->icon }}"></i>
                <p>
                  {{ $menu->name }}
                  <i class='right {{(!empty($menu->children))?"fas fa-angle-left":""}}'></i>
                </p>
              </a>
              @if(!empty($menu->children))
                <ul class="nav nav-treeview">
                  @foreach($menu->children as $child)
                    <li data-id='{{$child->id}}' class="nav-item">
                      <a  href="{{$child->path}}" class="nav-link">
                        <i class="{{ $child->icon }} child_nav"></i>
                        <p>{{ $child->name }}</p>
                      </a>
                    </li>
                  @endforeach 
                </ul>
              @endif
            </li>
          @endforeach
        @endif

          <?php
            $maintenance = WebHelper::sidebarMenu(1);
          ?>

        @if(count($maintenance) > 0)
         <li class="nav-header">Maintenance</li>
          @foreach($maintenance as $menu)
            <?php $href = (empty($menu->children))?" href= '$menu->path'":'';    ?>
            <li data-id='{{$menu->id}}' class='nav-item {{(!empty($menu->children))?"treeview":""}}'>
              <a <?php echo $href; ?> class="nav-link ">
                <i class="nav-icon {{ $menu->icon }}"></i>
                <p>
                  {{ $menu->name }}
                  <i class='right {{(!empty($menu->children))?"fas fa-angle-left":""}}'></i>
                </p>
              </a>
              @if(!empty($menu->children))
                <ul class="nav nav-treeview">
                  @foreach($menu->children as $child)
                    <li data-id='{{$child->id}}' class="nav-item">
                      <a  href="{{$child->path}}" class="nav-link">
                        <i class="{{ $child->icon }} child_nav"></i>
                        <p>{{ $child->name }}</p>
                      </a>
                    </li>
                  @endforeach 
                </ul>
              @endif
            </li>
          @endforeach
        @endif
        @if(MySession::myPrivilegeID() == 1)
          <li class="nav-header">Admin Access</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-circle"></i>
              <p>
                Account User
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" href="/user/index">
                  <i class="fas fa-bars nav-icon child_nav"></i>&nbsp;
                  <p>View Users</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/user/add" class="nav-link">
                  <i class="far fa-plus-square nav-icon child_nav"></i>&nbsp;
                  <p>Add User Account</p>
                </a>
              </li>
            </ul>
          </li>
          <!--/ Menu Management-->
          <li class="nav-item">
            <a href="/admin/menu_management" class="nav-link">
              <i class="nav-icon fas fa-bars"></i>
              <p>
                Menu Management
              </p>
            </a>
          </li>

<!--           <li class="nav-item">
            <a href="/admin/privilege/index" class="nav-link">
              <i class="nav-icon far fa-list-alt"></i>
              <p>Privilege Role</p>
            </a>
          </li> -->
          <li class="nav-item">
            <a class="nav-link">
              <i class="nav-icon far fa-list-alt"></i>
              <p>
                Privilege Role
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" href="/admin/privilege/index"  >
                  <i class="fas fa-bars nav-icon child_nav"></i>&nbsp;
                  <p >Privilege List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/privilege/add" class="nav-link">
                  <i class="far fa-plus-square nav-icon child_nav"></i>&nbsp;
                  <p>Add User Privilege</p>
                </a>
              </li>
            </ul>
          </li>
        @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->

    <style type="text/css">
      .child_nav{
          padding-left: 15px;
      }
    </style>

  </aside>


  <!--  -->

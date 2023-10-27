<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route("admin.main") }}" class="brand-link">
        <img src="{{ asset("storage/admin-panel.png") }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Admin Panel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('admin.roles.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            Roles
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.users.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.positions.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-suitcase"></i>
                        <p>
                            Positions
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.clients.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>
                            Clients
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.mailboxes.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Mailboxes
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.campaigns.index')}}" class="nav-link">
                        <i class="nav-icon fab fa-linkedin"></i>
                        <p>
                            Campaigns
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.projects.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Projects
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm mb-4">
                <div class="card-body border-bottom">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <select id="sort_by" class="form-select">
                                <option value="">All Time</option>
                                <option value="day">Today</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>

               

                        <div class="col-md-3">
                            <input type="text" id="search" class="form-control" placeholder="Search by User ID">
                        </div>

                        <div class="col-auto">
                            <button id="search_btn" class="btn btn-success w-100">Search</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('leaderboard.recalculate') }}" class="btn btn-primary w-100">Re-Calculate</a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered mb-0" id="actTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Points</th>
                                <th>Rank</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-body">
                            <!-- Fetched rows go here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        function loadData(params = {
            sort_by: $('#sort_by').val(),
            search: $('#search').val()
        }) {
            $.ajax({
                url: "{{ route('leaderboard.activies_data') }}",
                type: "GET",
                data: params,
                success: function (res) {
                    let tbody = $('#leaderboard-body');
                    tbody.empty();

                    if (res.length === 0) {
                        tbody.append('<tr><td colspan="4" class="text-center py-4 text-muted">No records found</td></tr>');
                        return;
                    }

                    res.rankedUsers.forEach((user, index) => {
                        tbody.append(`
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.total_points}</td>
                            <td>${user.rank}</td>
                        </tr>
                    `);
                    });
                    //convert to dtatable after 500 second
                    setTimeout(() => {
                        $("#actTable").DataTable();
                    }, 500);
                },
                error: function (err) {
                    console.log("Error fetching leaderboard:", err);
                }
            });
        }

        $(document).ready(function () {
            loadData(); // Initial load

            $('#search_btn').on('click', function () {
                loadData();
            });

            $('#sort_by, #user_select').on('change', function () {
                loadData();
            });

            //convert to datatable
            $("#actTable").DataTable();
        });
    </script>
@endpush
    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Input Section -->
        <section id="input">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Input Data</h5>
                </div>
                <div class="card-body">
                    <form id="dataForm">
                        <div class="mb-3">
                            <label for="dataInput" class="form-label">Enter Your Data:</label>
                            <textarea id="dataInput" class="form-control" rows="4" placeholder="Enter data in CSV format..."></textarea>
                        </div>
                        <button type="button" id="processData" class="btn btn-success">Process Data</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Results Section -->
        <section id="results">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Results</h5>
                </div>
                <div class="card-body">
                    <p id="resultText" class="text-muted">No data processed yet.</p>
                </div>
            </div>
        </section>
    </div>
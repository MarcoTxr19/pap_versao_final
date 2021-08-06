<div class="container">
{form_val}
<div class="col-sm-12">
{breadcrumb}
</div>

    <div class="row">
        
        <div class="col-sm-12">
            <form method="POST">

                <div class="form-group" enctype="multipart/form-data">
                    <label> Select the type of problem you have: </label>
                    <div class="col">
                        <select id="tipoContacto" name="tipoContacto" class="form-select" aria-label="Default select example">
                            <option value="Question">Question</option>
                            <option value="BugReport">Bug Report</option>
                            <option value="Other"> Other</option>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label>Describe the problem:</label>
                    <div class="col">
                        <textarea id="msgContacto" name="msgContacto" placeholder="Describe" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="col">
                    <input type="submit" value="Send"/>
                </div>
            </form>
        </div>
    </div>
</div>
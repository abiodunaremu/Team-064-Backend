
        <h2> User Session Services </h2>
        These are APIs used perform operations that has to do with system user
        <br/><br/>
        <h3>Login user API</h3>
        <div class="row">
            <div class="col-2"><strong>URL</strong></div>        
            <div class="col-10">
            <a href='http://yourbooks.milliscript.com/api/sessions'>yourbooks.milliscript.com/api/sessions </a>
            <!-- <a href='http://localhost:81//yourbooks/api/user'>yourbooks.milliscript.com/api/user </a> -->
            </div>        
        </div>
        <div class="row">
            <div class="col-2"><strong>Method</strong></div>        
            <div class="col-10">POST</div>        
        </div>
    
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">index</th>
                    <th scope="col">Parameter</th>
                    <th scope="col">Value</th>
                </tr>
            </thead>
            <tr>
                <th scope="row">1</td><td>username</td><td>string</td>
            </tr>
            <tr>
                <th scope="row">2</td><td>password</td><td>string</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>requesttype</td>
                <td>post</td>
            </tr>
        </table>

        <h3>Logout user API</h3>
        <div class="row">
            <div class="col-2"><strong>URL</strong></div>        
            <div class="col-10">
            <a href='http://yourbooks.milliscript.com/api/sessions'>yourbooks.milliscript.com/api/sessions </a>
            <!-- <a href='http://localhost:81//yourbooks/api/user'>yourbooks.milliscript.com/api/user </a> -->
            </div>        
        </div>
        <div class="row">
            <div class="col-2"><strong>Method</strong></div>        
            <div class="col-10">POST</div>        
        </div>
    
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">index</th>
                    <th scope="col">Parameter</th>
                    <th scope="col">Value</th>
                </tr>
            </thead>
            <tr>
                <th scope="row">1</td><td>sessionid</td><td>string</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>requesttype</td>
                <td>put</td>
            </tr>
        </table>
        
        <br/>
        <hr/>
        <br/>


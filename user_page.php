<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- DATABASE CONNECTION ---
$conn = new mysqli("localhost", "root", "", "users_b");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- DELETE DECK ---
if (isset($_POST['delete_deck'])) {
    $deck_id = intval($_POST['delete_deck_id']);

    // Delete flashcards first
    $stmt1 = $conn->prepare("DELETE FROM flashcards WHERE deck_id=?");
    $stmt1->bind_param("i", $deck_id);
    $stmt1->execute();
    $stmt1->close();

    // Delete the deck itself
    $stmt2 = $conn->prepare("DELETE FROM decks WHERE id=? AND user_id=?");
    $stmt2->bind_param("ii", $deck_id, $user_id);
    $stmt2->execute();
    $stmt2->close();

    header("Location: user_page.php");
    exit();
}

// --- ADD NEW DECK ---
if (isset($_POST['add_deck'])) {
    $deck_name = trim($_POST['deck_name']);
    if ($deck_name) {
        $stmt = $conn->prepare("INSERT INTO decks(user_id, deck_name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $deck_name);
        $stmt->execute();
        $stmt->close();
    }
}

// --- ADD NEW FLASHCARD ---
if (isset($_POST['add_flashcard'])) {
    $deck_id = intval($_POST['deck_id']);
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    if ($question && $answer) {
        $stmt = $conn->prepare("INSERT INTO flashcards(deck_id, question, answer) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $deck_id, $question, $answer);
        $stmt->execute();
        $stmt->close();
    }
}

// --- FETCH DECKS ---
$decks = $conn->query("SELECT * FROM decks WHERE user_id=$user_id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flashcards App</title>
    <link rel="stylesheet" href="user_page.css">
</head>
<body>
<h1>Flashcards App</h1>

<h2>Add Deck</h2>
<form method="POST">
    <input type="text" name="deck_name" placeholder="Deck name" required>
    <button name="add_deck">Add Deck</button>
</form>

<hr>

<h2>Your Decks</h2>
<?php while ($deck = $decks->fetch_assoc()): ?>
<div class="deck">
    <h3><?= htmlspecialchars($deck['deck_name']) ?></h3>

    <!-- Delete deck button -->
    <form method="post" onsubmit="return confirm('Delete this deck and all its flashcards?');">
        <input type="hidden" name="delete_deck_id" value="<?= $deck['id'] ?>">
        <button type="submit" name="delete_deck">Delete Deck</button>
    </form>

    <!-- Add flashcard form -->
    <form method="POST">
        <input type="hidden" name="deck_id" value="<?= $deck['id'] ?>">
        Question: <input type="text" name="question" required>
        Answer: <input type="text" name="answer" required>
        <button name="add_flashcard">Add Flashcard</button>
    </form>

    <!-- Quiz button -->
    <button onclick="startQuiz(<?= $deck['id'] ?>)">Start Quiz</button>
    <div id="quiz-<?= $deck['id'] ?>" class="quiz-container"></div>
</div>
 <div style="text-align: center;">
        <button onclick="window.location.href='logout.php'" style="width: 200px;">Logout</button>
    </div>
<?php endwhile; ?>

<script>
// Start quiz for a deck
function startQuiz(deckId) {
    const container = document.getElementById("quiz-" + deckId);
    container.style.display = 'block';
    container.innerHTML = "<h4>Loading quiz...</h4>";

    fetch("get_flashcards.php?deck_id=" + deckId)
    .then(response => response.json())
    .then(data => {
        if(data.length === 0) {
            container.innerHTML = "<p>No flashcards in this deck.</p>";
            return;
        }

        let html = `<form id="quiz-form-${deckId}">`;
        data.forEach(card => {
            html += `
                <div class="quiz-card">
                    <b>${card.question}</b><br>
                    <input type="text" data-answer="${card.answer}" class="user-answer">
                </div>`;
        });
        html += `<br><button type="button" onclick="submitQuiz(${deckId})">Submit Quiz</button></form>`;
        container.innerHTML = html;
    })
    .catch(err => {
        console.error(err);
        container.innerHTML = "<p>Error loading quiz.</p>";
    });
}

// Submit quiz and give instant feedback
function submitQuiz(deckId) {
    const form = document.getElementById("quiz-form-" + deckId);
    const inputs = form.querySelectorAll(".user-answer");
    let score = 0;

    inputs.forEach(input => {
        if (input.value.trim().toLowerCase() === input.dataset.answer.toLowerCase()) {
            score++;
            input.classList.add("correct");
        } else {
            input.classList.add("incorrect");
        }
        input.disabled = true;
    });

    const total = inputs.length;
    form.insertAdjacentHTML('beforeend', `<p>Score: ${score} / ${total}</p>`);

    // Save score via fetch
    fetch("save_score.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ deck_id: deckId, score: score, total: total })
    });
}


</script>
</body>
</html>